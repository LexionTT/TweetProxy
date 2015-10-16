<?php

// src/AppBundle/Entity/User.php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $name;

    /**
     * @ORM\Column(type="string", length=100)
     */
    protected $screen_name;

    /**
     * @ORM\Column(type="integer")
     */
    protected $user_id;

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
     * Set name
     *
     * @param string $name
     *
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set screenName
     *
     * @param string $screenName
     *
     * @return User
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
     * Set userId
     *
     * @param integer $userId
     *
     * @return User
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;

        return $this;
    }

    /**
     * Get userId
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }
}
