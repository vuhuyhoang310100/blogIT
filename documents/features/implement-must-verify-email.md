# Laravel Queue Email Verify and Password Reset Mails

---

## I. Overwrite the default methods in your User Model

We are still using the default **Laravel Notification**

### Edit `App/Models/User.php`

```php
<?php

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Auth\Notifications\ResetPassword;
class User extends Authenticatable implements MustVerifyEmail
{
 ...

  /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new VerifyEmail());
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

 }
```

## II. Create Custom Notification Classes that Extend the default Classes, and Queue the custom classes

Run command

 ```bash
php artisan make:notification Auth/VerifyEmail
php artisan make:notification Auth/ResetPassword
```

### II.1 Email Verification

File `App\Notifications\Auth\VerifyEmail.php`
**Note:** Don't forget extends `VerifyEmail`

```php
<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyEmail extends VerifyEmailNotification implements ShouldQueue
{
    use Queueable;

    public function __construct()
    {
        // Specifying queue is optional but recommended
        $this->queue = 'auth';
    }
}
```

### II.2 Password Reset

File `App\Notifications\Auth\ResetPassword.php`
**Note:** Don't forget extends `ResetPassword`

```php
<?php

namespace App\Notifications\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends ResetPasswordNotification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token)
    {
        // Specifying queue is optional but recommended
        $this->queue = 'auth';
    }
}
```

## III. Update the methods in your User Model to use your custom classes

```php
# App/Models/User.php

<?php

class User extends Authenticatable implements MustVerifyEmail
{
 ...

  /**
     * Send the email verification notification.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\Auth\VerifyEmail());
    }

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\Auth\ResetPassword($token));
    }

}
```

## IV. Run a worker to check if everything’s playing out

Default

```bash
php artisan queue:work
```

 **Note:** *If you specified a queue name in your constructor*

```bash
php artisan queue:work --queue=auth
```

 You should Edit The passwords config and change the throttling message to a better one.

```bash
resources/lang/en/passwords.php
```

**Example:**

 ```php
 'throttled' => 'You\'ve requested a password reset recently, please wait before retrying.',
 ```
