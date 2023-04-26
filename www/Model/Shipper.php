<?php
class Shipper extends User
{
    public readonly Hub $hubId;

    public function __construct(string $username, string $password, string $picURL, Hub $hub)
    {
        parent::__construct(Role::Shipper, $username, $password, $picURL);
        $this->hubId = $hub;
    }

    public function toArray(): array
    {
        return array(
            Role::Shipper->value, $this->username,
            $this->password, parent::getImageURL(), $this->hubId->hubId
        );
    }
}
