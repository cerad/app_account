<?php
namespace FOS\UserBundle\Entity;

/* ======================================================
 * The original FOSUserBundle did some sort of majic to allow extending driectly from Model/User
 * 
 * However, the simple default way requires User to live in the Entity directory
 * Probably a case of adding a mapping directory to setup file
 * 
 * But this works for now and is a bit clearer to me
 */
use FOS\UserBundle\Model\User as BaseUser;

class User extends BaseUser
{
    
}
?>
