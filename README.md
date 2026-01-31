# Filament Member

[![Latest Version on Packagist](https://img.shields.io/packagist/v/alessandronuunes/filament-member.svg?style=flat-square)](https://packagist.org/packages/alessandronuunes/filament-member)
[![Total Downloads](https://img.shields.io/packagist/dt/alessandronuunes/filament-member.svg?style=flat-square)](https://packagist.org/packages/alessandronuunes/filament-member)
[![License](https://img.shields.io/packagist/l/alessandronuunes/filament-member.svg?style=flat-square)](https://packagist.org/packages/alessandronuunes/filament-member)

A comprehensive Filament plugin for managing tenant members, invitations, and role-based access control in multi-tenant applications.

![Members Management Interface](screenshots/members.jpg)

## Features

- 🎯 **Member Management**: Easily manage members within your tenant/organization
- 📧 **Invitation System**: Send invitations via email or shareable links
- 🔐 **Role-Based Access**: Built-in support for roles (Owner, Admin, Member)
- 🌐 **Multi-language Support**: Includes English and Portuguese (Brazil) translations
- ⚙️ **Highly Configurable**: Customize models, tables, routes, and behavior
- 📱 **Responsive UI**: Beautiful, modern interface built with Filament
- 🔔 **Notifications**: Email notifications and in-app notifications for invitations
- ✅ **Validation**: Prevents duplicate invitations and membership conflicts

## Requirements

- PHP 8.3 or higher
- Laravel 11.x or 12.x
- Filament 4.x or 5.x

## Installation

Install the package via Composer:

```bash
composer require alessandronuunes/filament-member
```

Publish the configuration, migrations, and translations:

```bash
php artisan filament-member:install
```

Or publish them individually:

```bash
php artisan vendor:publish --tag=filament-member-config
php artisan vendor:publish --tag=filament-member-migrations
php artisan vendor:publish --tag=filament-member-translations
```

Run the migrations:

```bash
php artisan migrate
```

## Configuration

### Register the Plugin

Add the plugin to your Filament panel configuration:

```php
use AlessandroNuunes\FilamentMember\MemberPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->plugins([
            MemberPlugin::make(),
        ]);
}
```

### Customize Configuration

Edit `config/filament-member.php` to customize:

- **Models**: Change the User, Tenant, and TenantInvite models
- **Enums**: Set a custom tenant role enum
- **Tables**: Customize database table names and relationship columns
- **Tenancy**: Filament tenancy (slug, ownership, route prefix)
- **Routes**: Invitation accept path, name, and middleware
- **Invites**: Default role, expiration days, require registration
- **Notifications**: Send invite email, queue, from address
- **Validation**: Require role on invite

## Usage

### Managing Members

Once installed, a "Members" page will be available in your Filament panel navigation. From there you can:

1. **Invite Members**: Add up to 5 email addresses at once with assigned roles
2. **View Members**: See all current members with their roles and join dates
3. **Manage Roles**: Change member roles (Owner, Admin, Member)
4. **Remove Members**: Remove members from the organization
5. **View Pending Invitations**: See all pending invitations and resend or cancel them

### Invitation Flow

1. **Send Invitation**:
   - Individual invitations: Send to specific email addresses
   - Generic invitations: Generate a shareable link that anyone can use

2. **Accept Invitation**:
   - New users: Create an account and automatically join
   - Existing users: Login and automatically accept the invitation

3. **Notifications**:
   - Email notifications sent to invited users
   - In-app notifications for existing users

### Roles

The plugin includes three default roles:

- **Owner**: Full control, cannot be removed or have role changed
- **Admin**: Can invite and remove members
- **Member**: Basic access

You can customize these roles by modifying the `TenantRole` enum.

## Customization

### Custom Models

Update the model classes in `config/filament-member.php`:

```php
'models' => [
    'user' => App\Models\User::class,
    'tenant' => App\Models\Organization::class,
    'tenant_invite' => App\Models\Invitation::class,
],
```

### Custom Roles

Use your own role enum by setting it in `config/filament-member.php`:

```php
'enums' => [
    'tenant_role' => App\Enums\TenantRole::class,
],
```

Your enum should use string backing values (e.g. `owner`, `admin`, `member`) and implement Filament's `HasLabel` and `HasColor` for the admin UI. To add or change roles, define cases in your enum (e.g. `case Moderator = 'moderator';`).

### Custom Routes

Change the invitation acceptance route in `config/filament-member.php`:

```php
'routes' => [
    'invite_accept_path' => '/invite/{token}/accept',
    'invite_accept_name' => 'invite.accept',
    'invite_accept_middleware' => ['signed'],
],
```

### Theme / Styling

If the theme file does not exist yet (e.g. `resources/css/filament/admin/theme.css`), create it by running:

```bash
php artisan make:filament-theme admin
```

You can specify your panel name if different. See the [Filament documentation on creating a custom theme](https://filamentphp.com/docs/4.x/styling/overview#creating-a-custom-theme) for details.

For the plugin styles to work correctly in your Filament panel, add the `@source` directive to your theme file (e.g. `resources/css/filament/admin/theme.css`):

```css
@source '../../../../vendor/alessandronuunes/filament-member/resources/views/filament/**/*';
```

This ensures Tailwind scans the plugin's Blade views and generates the required CSS classes.

### Custom Views

To override the plugin views, copy the Blade files from `vendor/alessandronuunes/filament-member/resources/views` to your project (e.g. `resources/views/vendor/filament-member`) and edit them. Your copies will take precedence over the package views.

## Translations

The plugin includes translations for:

- English (`en`)
- Portuguese - Brazil (`pt_BR`)

To add more languages, publish the translations and add your language files:

```bash
php artisan vendor:publish --tag=filament-member-translations
```

After publishing, translation files are located in `lang/vendor/filament-member/{locale}/default.php`.

## Database Structure

The plugin creates three tables:

### `tenants`
- `id`
- `user_id` (owner)
- `name`
- `slug`
- `invitation_token` (nullable, for generic invite links)
- `status`
- `timestamps`

### `tenant_user` (pivot table)
- `id`
- `tenant_id`
- `user_id`
- `role`
- `timestamps`

### `tenant_invites`
- `id`
- `tenant_id`
- `user_id` (inviter)
- `email`
- `token`
- `role`
- `expires_at`
- `accepted_at`
- `timestamps`

## Events

The plugin dispatches the following events:

- `TenantInviteCreated`: Fired when a new invitation is created

## Listeners

- `SendTenantInviteNotification`: Sends email and in-app notifications
- `AcceptPendingInviteAfterLogin`: Automatically accepts pending invitations after login

## Validation Rules

- `AlreadyMember`: Prevents inviting users who are already members

## API

### ConfigHelper

Use the `ConfigHelper` class to access configuration values:

```php
use AlessandroNuunes\FilamentMember\Support\ConfigHelper;

$userModel = ConfigHelper::getUserModel();
$tenantModel = ConfigHelper::getTenantModel();
$defaultRole = ConfigHelper::getInviteConfig('default_role');
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This package is open-sourced software licensed under the [MIT license](LICENSE.md).

## Author

**Alessandro Nuunes**

- Email: alessandronuunes@gmail.com

## Support

For issues, questions, or contributions, please open an issue on the [GitHub repository](https://github.com/alessandronuunes/filament-members).

## Repository

- **GitHub**: [https://github.com/alessandronuunes/filament-members](https://github.com/alessandronuunes/filament-members)
- **Issues**: [Report a bug or request a feature](https://github.com/alessandronuunes/filament-members/issues)
- **Pull Requests**: [Contribute to the project](https://github.com/alessandronuunes/filament-members/pulls)
