<?php

// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="tweets")
 */
class Tweet
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="text")
     */
    protected $tweet;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $screen_name;

    /**
     * @ORM\Column(type="string", length=50)
     */
    protected $created_at;




    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tweet
     *
     * @param string $tweet
     *
     * @return Tweet
     */
    public function setTweet($tweet)
    {
        $this->tweet = $tweet;

        return $this;
    }

    /**
     * Get tweet
     *
     * @return string
     */
    public function getTweet()
    {
        return $this->tweet;
    }

    /**
     * Set screenName
     *
     * @param string $screenName
     *
     * @return Tweet
     */
    public function setScreenName($screenName)
    {
        $this->screen_name = $screenName;

        return $this;
    }

    /**
     * Get screenName
     *
     * @return string
     */
    public function getScreenName()
    {
        return $this->screen_name;
    }

    /**
     * Set createdAt
     *
     * @param string $createdAt
     *
     * @return Tweet
     */
    public function setCreatedAt($createdAt)
    {
        $this->created_at = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }
}
