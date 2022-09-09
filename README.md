## Laravel - CV CRM

[![Downloads](https://img.shields.io/packagist/dt/agenciafmd/laravel-cvcrm.svg?style=flat-square)](https://packagist.org/packages/agenciafmd/laravel-cvcrm)
[![Licença](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)

- Envia as conversões para o CV CRM
## Instalação

```bash
composer require agenciafmd/laravel-cvcrm:dev-master
```

## Configuração

Para que a integração seja realizada, precisamos do **token, email e url**

Para isso, vamos em **Configurações > Integrações**

![Configurações > Integrações](https://github.com/agenciafmd/laravel-cvcrm/raw/master/docs/screenshot03.png "Configurações > Integrações")

Agora, vamos em **APIS > Configurar**

![APIS > Configurar](https://github.com/agenciafmd/laravel-cvcrm/raw/master/docs/screenshot04.png "APIS > Configurar")

Preenchemos o formulário e clicamos em gravar configuração

![APIS > Configurar](https://github.com/agenciafmd/laravel-cvcrm/raw/master/docs/screenshot01.png "APIS > Configurar")

O sistema irá gerar o token e fornecer os dados que precisamos para a integração

![APIS > Configurar](https://github.com/agenciafmd/laravel-cvcrm/raw/master/docs/screenshot02.png "APIS > Configurar")

Colocamos esses dados no nosso .env

```dotenv
CVCRM_TOKEN=929E609F80B7320AC31B2BE6CDA9913C13216D64
CVCRM_EMAIL=user@fmd.ag
CVCRM_TOKEN=https://xxxxxxxx.cvcrm.com.br/
```

## Uso

Envie os campos no formato de array para o SendConversionsToCvcrm.

O campo **email** é obrigatório =)

```php
use Agenciafmd\Cvcrm\Jobs\SendConversionsToCvcrm;

$data['email'] = 'carlos@fmd.ag';
$data['nome'] = 'Carlos Seiji';
$data['telefone'] = '(17) 99999-9999';


SendConversionsToCvcrm::dispatch($data)
    ->delay(5)
    ->onQueue('low');
```

Note que no nosso exemplo, enviamos o job para a fila **low**.

Certifique-se de estar rodando no seu queue:work esteja semelhante ao abaixo.

```shell
php artisan queue:work --tries=3 --delay=5 --timeout=60 --queue=high,default,low
```