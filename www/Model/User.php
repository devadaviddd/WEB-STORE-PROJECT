<?php

class User
{
    public readonly string $username;
    public readonly string $password;
    public readonly string $fullname;
    private $pictureURL;
    public readonly Role $role;

    /**
     * 
     */
    public function __construct($role, $username, $password, $pictureURL)
    {
        $this->username = $username;
        $this->password = $password;
        $this->pictureURL = $pictureURL;
        $this->role = $role;
    }
    /**
     * 
     */
    public function logout(): bool
    {

        return false;
    }

    public function getImageURL(): string
    {
        return $this->pictureURL;
    }

    public function updatePictureURL(string $url)
    {
        $this->pictureURL = $url;
    }
}
