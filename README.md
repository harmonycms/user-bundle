UserBundle
==========
This bundle, simple yet convenient, lets you easily add to your application features such as:
- user registration
- user authentication
- password resetting
- user account locking
- user account expiration
- force a user to reset his password at first connection

**Notes**:
- this bundle is full decoupled from Doctrine any abstraction layer.
- this bundle provide Doctrine mapping configuration for ORM and MongoDB.
- this bundle assumes you're using Doctrine to persist and retrieve your users. It provides a Doctrine UserProvider.


Configuration
=============

## Step 1: Configure the bundle
Create a file named `harmony_user.yaml` in the `config/packages` directory with the following content:

```yaml
# config/packages/harmony_user.yaml
harmony_user:
    user_class: App\Entity\User         # FQDN name of your user class
    password_reset:
        email_from: john.doe@gmail.com  # Sender of the password reset requests
        token_ttl: 7200                 # TTL of a password reset request
```

**Notes**:
- By default, the bundle assumes your User class name is `App\Entity\User`

## Step 2: Setup routes
This bundle provide the recipe file `config/routes/harmony_user.yaml` with the following content:

```yaml
# config/routes.yaml
harmony_user:
    resource: "@HarmonyUserBundle/Controller/"
    type: annotation
```

## Step 3: Enable the bundle for your firewall

Edit the `config/packages/security.yaml` file and add the appropriate blocks/rules as shown below, in this
minimal configuration example.

```yaml
# config/packages/security.yaml
security:
    encoders:
        App\Entity\User:
            algorithm: bcrypt

    role_hierarchy:
        ROLE_ADMIN:       ROLE_USER
        ROLE_SUPER_ADMIN: ROLE_ADMIN

    providers:
        user_provider:
            id: Harmony\Bundle\UserBundle\Security\UserProvider

    firewalls:
        dev:
            pattern: ^/(_(profiler|wdt)|css|images|js)/
            security: false

        main:
            pattern:  ^/
            provider: user_provider
            user_checker: Harmony\Bundle\UserBundle\Security\UserChecker
            anonymous:    true

            form_login:
                login_path: harmony_user_login
                check_path: harmony_user_login

            logout: true


    # Easy way to control access for large sections of your site
    # Note: Only the *first* access control that matches will be used
    access_control:
        - { path: ^/login, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/password-reset, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/lost-password, role: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin/, role: ROLE_ADMIN }
```

## Step 4: User class
This bundle provide a default entity class `App\Entity\User`, configured by a Symfony recipe.
This class already extends the abstract mapped entity `Harmony\Bundle\UserBundle\Model\User` with the following content:

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Harmony\Bundle\UserBundle\Model\User as BaseUser;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="user")
 * @UniqueEntity(fields="email", message="Email already registered.")
 * @UniqueEntity(fields="username", message="Username already registered.")
 */
class User extends BaseUser
{

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank()
     */
    private $fullName;

    public function getId(): int
    {
        return $this->id;
    }

    public function setFullName(string $fullName): void
    {
        $this->fullName = $fullName;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }
}
```
