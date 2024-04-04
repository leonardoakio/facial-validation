<p align="center">
&nbsp;
    <img src="https://img.shields.io/badge/version-v1.0.0-blue"/>
    <img src="https://img.shields.io/github/contributors/leonardoakio/facial-validation"/>
    <img src="https://img.shields.io/github/stars/leonardoakio/facial-validation?style=sociale"/>
    <img src="https://img.shields.io/github/forks/leonardoakio/facial-validation?style=social"/>
    <img src="https://img.shields.io/badge/License-MIT-blue"/>
</p>

## Simple Keycloak Client para Laravel
*Legacy CodeIgniter*: Dados são recebidos de forma bruta por string JSON
`"data:image/jpeg;base64,/9j/4AA...AAADE/2Q=="` enviados por um front-end por Ajax passando as fotos em base64 por  `JSON.stringify`
```javascript
async sendImages() {
    $.ajax({
        url: urlBase + "index/validatePhoto",
        type: 'POST',
        contentType: 'application/json',
        data: JSON.stringify(this.capturedImages),
        success: (data) => {
            this.responseData = data
            this.verified = (JSON.parse(data.data).verified);
            this.capturedImages = [];
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
        }
    });
}
```

*Laravel*: Os dados não necessariamente precisarão ser enviados de forma bruta com `JSON.stringify()`, sendo enviados e recebidos como JSON
```javascript
const response = await api.post(
    urlBase + "index/validatePhoto", 
    this.capturedImages, {
        headers: {
        'Content-Type': 'application/json'
        }
    }
);
```

## Iniciando o projeto
Fazer o require do pacote que será importado ao projeto para dentro da pasta `vendor`
```shell
composer require leonardoakio/facial-validation
```
Ou se já estiver instalado, realizar o update do pacote para verificar se não há alguma nova versão
```shell
composer update leonardoakio/facial-validation
```
Inserir o provider dentro do arquivo `config/app.php` para que KeycloakClientServiceProvider possa ser identificado fora do contexto da vendor
```php
FacialValidator\FacialValidatorServiceProvider::class
```
Publicar os arquivos a serem gerados da lib
```shell
php artisan vendor:publish --tag=facial-laravel
```
Conseguimos executar o Provider de forma direta também
```shell
php artisan vendor:publish  --provider="FacialValidator\FacialValidatorServiceProvider"
```
Adicionar a ENV `FACIAL_FRAMEWORK` que servirá para publicar os arquivos ou do Laravel ou Legacy (Code Igniter) (Opcional)
```php
FACIAL_FRAMEWORK=laravel
```
Vincular o `FacialValidatorRepositoryInterface` ao `FacialValidatorRepository` no arquivo `app/Providers/AppServiceProvider.php` que será a comunicação com o DeepFace, podendo se estender a outras ferramentas de conhecimento facial (Amazon Rekognition), bastando apenas implementar a mesma interface e criar um novo bind para cada ferramenta
```php
$this->app->bind(
    App\Repositories\FacialValidator\FacialValidatorRepositoryInterface::class,
    \App\Repositories\FacialValidator\FacialValidatorRepository::class
);
```
Adicionar a rota de validação no arquivo de rotas do Laravel `routes/api.php`
```php
Route::group(["prefix" => "facial"], function () {
    Route::post('/verify', [App\Http\Controllers\FacialValidator\FacialValidationController::class, 'validatePhoto']);
});
```

*OBS*: A inclusão dos últimos itens estão disponíveis no `FacialValidatorServiceProvider` e basta a implementação de uma lógica para executar o boot apenas na inicialização e descomentar a chamada dos metodos (`registerRoutes()`, `registerBindings()`) para automatizar os processos manuais anteriores

### Estrutura
```bash
├── config/                 # Configuração para definir boot do FacialServiceProvider.php
├── routes/                 # Rotas de autenticação do Keycloak
├── src/Controllers         # Controladores que irão lidar com as rotas
├── src/Enums               # Lista de elementos 
├── src/Repositories        # Classe de comunicação com o Keyclaok
├── composer.json           # Listar as dependências do projeto e suas versões
```

### Guias:
- [Deepface](https://pypi.org/project/deepface/0.0.24/)
- [Getting Started on Amazon Rekognition ](https://medium.com/analytics-vidhya/getting-started-on-amazon-rekognition-and-using-their-sdks-9b8e7dee3048)