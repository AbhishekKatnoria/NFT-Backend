## NFT-Backend
- Production URL 
   ```
   https://aft-frontend.com/
   ```
### Installation (Production Server)
1. Clone your project
2. Go to the folder application using cd command on your cmd or terminal
3. Run the composer install command on your cmd or terminal
   ```
   composer install
   ```
5. Copy .env.example file to .env on the root folder. You can type copy .env.example .env if using command prompt Windows or cp .env.example .env if using terminal, Ubuntu
6. Open your .env file and change the database name (DB_DATABASE) to whatever you have, username (DB_USERNAME) and password (DB_PASSWORD) field correspond to your configuration.
7. Run command for genrate app key
   ```
   php artisan key:generate
   ```
8. Copy command to run other tables  
   ```
   php artisan migrate
   ```
9. Copy command to run server
   ```
   php artisan serve
   ```

