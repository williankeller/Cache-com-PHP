# Cache com PHP
Sistema de Cache de memória com PHP.
Este recurso pode ser usado com respostas do banco de dados utimizando as buscas externas.

### Uso

**Instanciar a classe Cache**
```php
$cache = new Cache();
```

**Salvando Conteúdo**
```php
$cache->salva("nome-da-chave", "conteudo-do-cache");
```
O conteúdo pode ser uma `string` ou até mesmo um `array`

**Recuperando Conteúdo**
```php
$cache->recupera("nome-da-chave");
```

**Definição da Pasta do Cache**
O sistema dá a opção de você escolher a pasta de destino dos arquivos temporários.
```php
$pasta = "";
```

** Caso a mesma não seja definida, o sistema receberá a pasta temporária padrão do servidor **
```php
sys_get_temp_dir()
```
