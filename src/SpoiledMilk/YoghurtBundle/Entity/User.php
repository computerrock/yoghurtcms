<?php

namespace SpoiledMilk\YoghurtBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="SpoiledMilk\YoghurtBundle\Repository\UserRepository")
 * @UniqueEntity("username")
 * @UniqueEntity("email")
 */
class User implements AdvancedUserInterface {

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    
    /**
     *
     * @var string 
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     */
    private $username;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $password;
    
    private $newPassword;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=32)
     */
    private $salt;
    
    /**
     *
     * @var array
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank()
     */
    private $role;
    
    /**
     *
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $isActive;
    
    /**
     *
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;
    
    function __construct($username = null, $password = null, $salt = null, $role = null, $isActive = false, $email = null) {
        if($username)
            $this->username = $username;
        
        if($password)
            $this->password = $password;
        
        $this->newPassword = '';
        
        if($salt)
            $this->salt = $salt;
        else
            $this->salt = md5(uniqid (null, true));
        
        if($role)
            $this->role = $role;
        else
            $this->role = 'ROLE_EDITOR';
        
        $this->isActive = $isActive;
        
        if($email)
            $this->email = $email;
        
    }

    public function equals(UserInterface $user) {
        return $this->username === $user->getUsername();
    }

    public function eraseCredentials() {
        
    }
    
    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }
    
    public function getNewPassword() {
        return $this->newPassword;
    }

    public function setNewPassword($newPassword) {
        $this->newPassword = $newPassword;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function setSalt($salt = null) {
        if($salt)
            $this->salt = $salt;
        else
            $this->salt = md5(uniqid (null, true));
    }

    public function getRoles() {
        return array($this->role);
    }

    public function getRole() {
        return $this->role;
    }
    
    public function setRole($role) {
        $this->role = $role;
    }

    public function getIsActive() {
        return $this->isActive;
    }

    public function setIsActive($isActive) {
        $this->isActive = $isActive;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function isAccountNonExpired() {
        return true;
    }

    public function isAccountNonLocked() {
        return true;
    }

    public function isCredentialsNonExpired() {
        return true;
    }

    public function isEnabled() {
        return $this->isActive;
    }


}