run command for mobile network:
php artisan serve --host=0.0.0.0 --port=8000

To access the application from other devices on the same network, use your computer's local IP address (e.g., 192.168.1.[100]:8000).

✅ How to Run Laravel + Livewire Project on Another PC (Development Mode)
📌 1. Clone or Copy the Project

যদি Git ব্যবহার করেন:

git clone your-project-url


অথবা Zip করে অন্য PC-তে কপি করুন।

📌 2. Install PHP, Composer, Node.js

অন্য PC-তে অবশ্যই এগুলো install থাকতে হবে:

PHP 8+

Composer

Node.js (LTS)

MySQL / MariaDB (যদি database থাকে)

Windows হলে XAMPP/WAMP ও ব্যবহার করতে পারেন।

📌 3. Go to Project Directory
cd your-project-folder

📌 4. Install PHP Dependencies
composer install

📌 5. Install Node Dependencies
npm install

📌 6. Create .env File

যদি .env file না থাকে:

cp .env.example .env


Windows CMD:

copy .env.example .env

📌 7. Application Key Generate
php artisan key:generate

📌 8. Update .env for Database

Example:

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=mydb
DB_USERNAME=root
DB_PASSWORD=
APP_URL=xxxx.com

📌 9. Run Migration (If needed)

যদি database empty হয়:

php artisan migrate --seed

📌 10. Run Laravel Development Server
php artisan serve


Browser:

http://127.0.0.1:8000

📌 11. Run Livewire + Vite (Frontend)
npm run dev


এখন Livewire + Tailwind + Alpine.js সব কাজ করবে।

🔥 Common Issues & Fixes
❗Issue: Livewire view not updating

Fix:

php artisan view:clear
php artisan cache:clear
php artisan config:clear

❗Issue: Assets not loading on mobile/other PC

Use:

npm run dev -- --host


Or update vite.config.js:

export default defineConfig({
    server: {
        host: true,
    }
});

❗Issue: Storage images not showing

Run:

php artisan storage:link

❗Issue: Permissions (Linux)
sudo chmod -R 777 storage bootstrap/cache

🎯 If project runs but design not loading

এর কারণ সাধারণত:

Vite not running

Wrong asset path

Firewall block

Host not accessible

Fix:

npm run dev -- --host



Since your **mobile doesn’t know “localhost” on your PC**, those CSS/JS files fail to load.

---

## ✅ **Fix (for Vite)**

### 1️⃣ Edit your `vite.config.js`
Open your project root file `vite.config.js`  
and **add this server configuration**:

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        host: '0.0.0.0',        // allow network access
        port: 5173,             // or any port you like
        hmr: {
            host: '192.168.x.x', // your computer's local IP
        },
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
});

🧩 Step 2 — Run Laravel server bound to that IP

By default, Laravel’s artisan server only listens on 127.0.0.1 (localhost).
You need to tell it to listen on all network interfaces (0.0.0.0).

php artisan serve --host=0.0.0.0 --port=8000



# Run all seeders in dependency order
php artisan db:seed

# Or fresh migration with seeding
php artisan migrate:fresh --seed