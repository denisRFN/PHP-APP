# Basic Product CRUD App

This is a simple CRUD application for learning PHP, MySQL, HTML, CSS, JavaScript, jQuery, and AJAX.

No PHP frameworks, no Composer, and no Bootstrap are used.

## Files

- `index.php` - product list, search, pagination, delete modal
- `create.php` - create product form
- `edit.php` - edit product form
- `delete.php` - delete product logic
- `config.php` - database connection and helper functions
- `form_errors.php` - shared error display
- `style.css` - basic responsive design
- `script.js` - jQuery and AJAX form/delete behavior
- `database.sql` - database and table creation
- `uploads/` - uploaded images folder, created automatically if missing

## jQuery and AJAX

The project uses jQuery for AJAX requests:

- create product form submission
- edit product form submission
- delete product action from the confirmation modal

The PHP files still work without JavaScript because the normal form submissions are kept as a fallback.

## How to run with XAMPP

1. Copy the `product_crud_basic` folder into your XAMPP `htdocs` folder.
2. Start Apache and MySQL from the XAMPP Control Panel.
3. Open phpMyAdmin:

```text
http://localhost/phpmyadmin
```

4. Import `database.sql` in phpMyAdmin.
5. Check the database settings in `config.php`.
6. Open the app in the browser:

```text
http://localhost/product_crud_basic/
```

## Database settings

Default values in `config.php`:

```php
$host = 'localhost';
$dbName = 'product_crud_basic';
$username = 'root';
$password = '';
```

Change them only if your local MySQL uses different credentials.
