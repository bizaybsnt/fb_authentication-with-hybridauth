# Facebook Authentication with HybridAuth

## Installation <br />

### Composer<br />
  - Run `composer install` command to download composer packages
  
### Setup Facebook Application <br />
  - Edit keys and secret in `config.php` to use your own facebook application
  
##### Creating Own Facebook Application <br />
  - Create developers account from https://developers.facebook.com/
  - Add new App and Facebook will generate App ID and secret key for you.
  - Under Setting/Basic give site URL and save it. (For our case site URL is http://localhost:8000/)

### Setup Database
  - Edit database credentials in `config.php` 
  - Use `hybrid.sql` to import database
  
## Running Application
  - Change directory to public folder `cd public` on terminal
  - Run `php -S localhost:8000` command and visit `localhost:8000` on browser.
