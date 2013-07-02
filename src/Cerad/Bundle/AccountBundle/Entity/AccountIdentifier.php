<?php
namespace Cerad\Bundle\AccountBundle\Entity;

use Cerad\Bundle\AccountBundle\Functions\Guid;

class AccountIdentifier
{    
    protected $id;      // Guid for now, contains addisitonal profile info
    
    protected $value;
    protected $account; // aka entiry
    
    protected $name;    // aka user display name
    protected $source;  // aka provider name
    protected $profile;
   
    protected $status = 'Active';
    
    protected $createdOn;
    
    // This needs to go away
    static public function create($providerName, $displayName, $value, $profileData = null)
    {
        $item = new self();
        
        $item->setProviderName($providerName);
        $item->setDisplayName ($displayName);
        $item->setValue       ($value);
        $item->setProfile     ($profileData);
        
        return $item;
    }
    public function setName     ($value) { $this->name      = $value; }
    public function setValue    ($value) { $this->value     = $value; }
    public function setSource   ($value) { $this->source    = $value; }
    public function setStatus   ($value) { $this->status    = $value; }
    public function setAccount  ($value) { $this->account   = $value; }
    public function setProfile  ($value) { $this->profile   = $value; }
    public function setCreatedOn($value) { $this->createdOn = $value; }
    
    public function getName()      { return $this->name;      }
    public function getValue()     { return $this->value;     }
    public function getSource()    { return $this->source;    }
    public function getStatus()    { return $this->status;    }
    public function getAccount()   { return $this->account;   }
    public function getProfile()   { return $this->profile;   }
    public function getCreatedOn() { return $this->createdOn; }
    
    public function __construct()
    {
        $this->id = Guid::gen();
        
        $this->createdOn = new \DateTime();
    }
     
}
?>