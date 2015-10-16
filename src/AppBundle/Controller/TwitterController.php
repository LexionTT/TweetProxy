<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Utils\TwitterAPIExchange;
use AppBundle\Entity\User;
use AppBundle\Entity\Tweet;
use Doctrine\Common\Collections\ArrayCollection;

class TwitterController extends Controller
{
    public function indexAction(Request $request)
    {
        // Set repository
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        
        // Get all users - paginate(20)
        $users = $repository->findAll();
        $paginator  = $this->get('knp_paginator');
        $pagination = $paginator->paginate(
        $users,
        $request->query->getInt('page', 1)/*page number*/, 20/*limit per page*/);
        
        return $this->render('index.html.twig', array(
            'pagination' => $pagination));
    }
    
    public function showAction($screen_name)
    {

        /**
         * Twitter authentication data
         */
        $settings = array(
            'oauth_access_token' => "",
            'oauth_access_token_secret' => "",
            'consumer_key' => "",
            'consumer_secret' => ""
        );
        
        // Prepare data for API call
        $url = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getfield = '?screen_name='. $screen_name .'&count=20';
        $requestMethod = 'GET';
        
        // Create new Twitter connection
        $twitter = new TwitterAPIExchange($settings);
        // API call
        $data = $twitter->setGetfield($getfield)
            ->buildOauth($url, $requestMethod)
            ->performRequest();

        // Convert JSON output to array
        $data = json_decode($data);

        // If user doesn't exists at Twitter
        if (@empty($data) || @$data->errors || @$data->error) {
            $this->addFlash(
                'notice',
                'User '. $screen_name .' does not exists or has no tweets!'
            );
            return $this->redirectToRoute('homepage');
        }
        
        // Prepare array just with tweets for displaying
        foreach ($data as $tweet) {
            $tweets[] = $tweet->text;
        }
        
        /**
         * Database Part
         **/
         
        // Set repository
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
            
        $em = $this->getDoctrine()->getManager();
            
        // Check if user exist by id in the database
        // If not, add new record
        $user = $repository->findOneBy(array('user_id' => $data[0]->user->id));
        if (!$user) {
            // Prepare user data for storage
            $user = new User();
            $user->setName($data[0]->user->name);
            $user->setScreenName($data[0]->user->screen_name);
            $user->setUserId($data[0]->user->id);
    
            $em->persist($user);
            //$em->flush();    
        }
        
        // Get all tweet_id-s for current screen_name to avoid duplicate entries
         $query = $em->createQuery('SELECT t.created_at FROM AppBundle:Tweet t WHERE t.screen_name=:screen_name')->setParameter('screen_name', $screen_name);
        $tweetids = $query->getResult();
         
         // Count tweets, check if user has less than 20 tweets for displaying, pass variable to view as limit in for loop
         $tweetnum = (count($tweets) < 20 ? count($tweets) : 20);
        
         // We have multidimensional array
         // Get values to another one dimensional array
         $created_at = array();
        if (!empty($tweetids)) {
            foreach ($tweetids as $tid) {
                $created_at[] = $tid['created_at'];
            }
        }

        // Store tweets
        foreach ($data as $tweet) {
            // check if already exists tweet for that user, if not - add new
            if (!in_array($tweet->created_at, $created_at)) {
                $newTweet = new Tweet();
                $newTweet->setScreenName($data[0]->user->screen_name);
                $newTweet->setTweet($tweet->text);
                $newTweet->setCreatedAt($tweet->created_at);
                $em->persist($newTweet);
            }
        }
        
        $em->flush();
        $em->clear();
               
        return $this->render('user.html.twig', array(
        'screen_name' => $screen_name,
        'tweets'    => $tweets,
        'tweetnum'  => $tweetnum
        ));
    }
    
    public function searchAction(Request $request)
    {
        $result = '';
        // Set repository
        $repository = $this->getDoctrine()
            ->getRepository('AppBundle:User');
        
        // Get all users - paginate(20)
        $users = $repository->findAll();
        
        /**
         * if exists get data and according to existance create search query
         */
        $em = $this->getDoctrine()->getManager();
        // Basic part of query, other options will be concatenated
        $sql = "SELECT t.tweet FROM tweets t";
        
        // Only if tweet text exists -> screen_name should be '0'
        if (!empty($_GET['tweettext']) && $_GET['screen_name'] == '0') {
            $sql .= " WHERE MATCH (t.tweet) AGAINST (:sparam)";

            $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($sql);
    
            $stmt->bindValue('sparam', $_GET['tweettext'], \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
            $stmt->execute();
            $result = $stmt->fetchAll();
        }
        
        // Only if screen_name exists -> tweettext should be empty
        if (empty($_GET['tweettext']) && @$_GET['screen_name'] != '0') {
            $sql .= " WHERE t.screen_name = :screen_name";
            
            $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($sql);
    
            $stmt->bindValue('screen_name', @$_GET['screen_name'], \Doctrine\DBAL\Connection::PARAM_INT_ARRAY);
            $stmt->execute();
            $result = $stmt->fetchAll();
        }

        // If exists both tweettext and screen_name
        if (!empty($_GET['tweettext']) && @$_GET['screen_name'] != '0') {
            $sql .= " WHERE MATCH(t.tweet) AGAINST(:tweettext) AND t.screen_name = :screen_name";
            
            $stmt = $this->getDoctrine()->getEntityManager()
            ->getConnection()
            ->prepare($sql);
    
            $stmt->execute(array(
                ':screen_name'   => @$_GET['screen_name'],
                ':tweettext'     => @$_GET['tweettext']
            ));
            $result = $stmt->fetchAll();
        }
        
        // Pagination part if not empty result
        if (!empty($result)) {
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
            $result,
            $request->query->getInt('page', 1)/*page number*/, 20/*limit per page*/);
        }
        
        return $this->render('search.html.twig', array('users' => $users, 'pagination' => @$pagination));
    }
}
