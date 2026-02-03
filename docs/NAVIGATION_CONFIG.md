# Configuração de Navegação no Filament Member

Este documento descreve os passos para adicionar configuração de navegação (group, sort, icon, label) no plugin **filament-member**, seguindo o mesmo padrão usado no **filament-communicate**.

---

## Visão geral

No communicate, a navegação é configurável via `config/filament-communicate.php` na chave `navigation`, e cada Resource lê esses valores em `getNavigationGroup()`, `getNavigationSort()`, `getNavigationIcon()` e `getNavigationLabel()`.

No member, há apenas uma **Page** no menu (TenantMembers). O objetivo é permitir configurar:

- **group** – grupo do menu (ex.: "Organização", "Plugin")
- **sort** – ordem no menu (número)
- **icon** – ícone (ex.: `heroicon-o-users` ou enum no v4)
- **label** – texto exibido no menu (opcional; se não informado, usa tradução)

---

## Passo 1: Adicionar a chave `navigation` no config

**Arquivo:** `config/filament-member.php`

Inclua uma nova seção após as seções existentes (por exemplo, após `validation`):

```php
/*
|--------------------------------------------------------------------------
| Configurações de Navegação do Filament
|--------------------------------------------------------------------------
|
| Define as configurações de navegação para a página de membros do plugin.
| Permite customizar grupo, ordem, ícone e label no menu lateral.
|
*/
'navigation' => [
    'tenant_members_page' => [
        'group' => __('filament-member::default.navigation.group'),
        'sort' => 2,
        'icon' => 'heroicon-o-users',
        'label' => null, // null = usa __('filament-member::default.navigation.label')
    ],
],
```

- **group**: string ou retorno de `__()`. Ex.: `'Organização'` ou `__('filament-member::default.navigation.group')`.
- **sort**: inteiro. Menor número aparece primeiro.
- **icon**: string do Heroicon (outline). No Filament v4 pode ser enum `Heroicon::OutlinedUsers`; na config use a string equivalente para facilitar publicação do config.
- **label**: string ou `null`. Se `null`, a Page usa a tradução padrão.

---

## Passo 2: Ajustar a Page para ler da config

**Arquivo:** `src/Pages/TenantMembers.php`

### 2.1 Propriedades estáticas (valores padrão)

Mantenha ou defina valores padrão que serão usados quando a config não existir:

```php
protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

protected static ?int $navigationSort = 2;
```

### 2.2 getNavigationGroup()

Substituir o retorno fixo pela leitura da config, com fallback na tradução:

```php
#[Override]
public static function getNavigationGroup(): ?string
{
    $configGroup = config('filament-member.navigation.tenant_members_page.group');

    if ($configGroup !== null && $configGroup !== '') {
        return $configGroup;
    }

    return __('filament-member::default.navigation.group');
}
```

### 2.3 getNavigationLabel()

Fazer o label vir da config quando definido, senão da tradução:

```php
#[Override]
public static function getNavigationLabel(): string
{
    $configLabel = config('filament-member.navigation.tenant_members_page.label');

    if ($configLabel !== null && $configLabel !== '') {
        return $configLabel;
    }

    return __('filament-member::default.navigation.label');
}
```

### 2.4 getNavigationIcon()

Filament v4 usa enum para ícones em algumas APIs. Para manter compatibilidade com config em string (ex.: `'heroicon-o-users'`), use:

```php
#[Override]
public static function getNavigationIcon(): ?string
{
    $configIcon = config('filament-member.navigation.tenant_members_page.icon');

    if ($configIcon !== null && $configIcon !== '') {
        return $configIcon;
    }

    return static::$navigationIcon instanceof BackedEnum
        ? static::$navigationIcon->value
        : static::$navigationIcon;
}
```

Se a Page em v4 usar apenas enum, mantenha a propriedade estática como enum e, no retorno, converta a string da config para o valor aceito pelo Filament (geralmente a string já é aceita).

### 2.5 getNavigationSort()

Ler da config com fallback no valor estático:

```php
#[Override]
public static function getNavigationSort(): ?int
{
    $configSort = config('filament-member.navigation.tenant_members_page.sort');

    return is_numeric($configSort) ? (int) $configSort : static::$navigationSort;
}
```

---

## Passo 3: Publicar e customizar o config na aplicação

Na aplicação que consome o plugin:

1. Publicar o config (se o plugin tiver comando ou instrução para isso):
   ```bash
   php artisan vendor:publish --tag=filament-member-config
   ```
2. Editar `config/filament-member.php` e ajustar a chave `navigation.tenant_members_page`:

```php
'navigation' => [
    'tenant_members_page' => [
        'group' => 'Organização',
        'sort' => 5,
        'icon' => 'heroicon-o-user-group',
        'label' => 'Membros',
    ],
],
```

Assim o item de menu fica no grupo "Organização", com ordem 5, ícone e label customizados.

---

## Passo 4: (Opcional) ConfigHelper para navegação

Para centralizar o acesso à config de navegação, você pode adicionar em `Support/ConfigHelper.php`:

```php
/**
 * Get a navigation configuration value.
 */
public static function getNavigationConfig(string $pageKey, string $key, mixed $default = null): mixed
{
    return config("filament-member.navigation.{$pageKey}.{$key}", $default);
}
```

Uso na Page:

```php
ConfigHelper::getNavigationConfig('tenant_members_page', 'group')
ConfigHelper::getNavigationConfig('tenant_members_page', 'sort', 2)
ConfigHelper::getNavigationConfig('tenant_members_page', 'icon')
ConfigHelper::getNavigationConfig('tenant_members_page', 'label')
```

---

## Resumo dos arquivos alterados

| Arquivo | Alteração |
|---------|-----------|
| `config/filament-member.php` | Adicionar a seção `navigation` com `tenant_members_page` (group, sort, icon, label). |
| `src/Pages/TenantMembers.php` | Implementar `getNavigationGroup()`, `getNavigationLabel()`, `getNavigationIcon()` e `getNavigationSort()` lendo da config com fallbacks. |
| `src/Support/ConfigHelper.php` | (Opcional) Método `getNavigationConfig()`. |

---

## Diferenças Communicate (v3) x Member (v4)

- **Communicate** usa **Resources**; cada Resource tem `getCluster()` opcional e lê `config('filament-communicate.navigation.{resource_key}.cluster')`.
- **Member** usa uma **Page**; em Filament v4/v5, Pages não usam cluster da mesma forma. Se no futuro o member tiver Resources ou Clusters, pode-se adicionar a chave `cluster` na config e um `getCluster()` no Resource, como no communicate.
- **Ícones**: no communicate (Filament v3) usa-se string (`'heroicon-o-envelope'`). No member (Filament v4) a Page pode usar enum `Heroicon::OutlinedUsers`; ao ler da config, retornar string é geralmente suficiente, pois o Filament aceita string para ícones.

---

## Exemplo completo da seção no config (member)

```php
'navigation' => [
    'tenant_members_page' => [
        'group' => __('filament-member::default.navigation.group'),
        'sort' => 2,
        'icon' => 'heroicon-o-users',
        'label' => null,
    ],
],
```

Com isso, a aplicação pode sobrescrever apenas o que precisar (por exemplo, só `group` e `sort`), e o restante continua vindo do default ou da tradução.
