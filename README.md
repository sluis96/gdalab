# GDALab


## Instalacion y Configuracion

Instalar las dependencias.
```sh
composer install
```

La estructura de la base de datos se encuentra en el archivo [mydb.sql](https://github.com/sluis96/gdalab/blob/main/mydb.sql)

Se hace uso de [jwt-auth](https://jwt-auth.readthedocs.io/en/develop/) para la autenticación, el siguiente comando genera la llave secreta:
```sh
php artisan jwt:secret
```
Esto actualizara su archivo `.env`

## Servicios

| Metodo | RUTA | Descripcion | Header |
| ------ | ------ | ------ | ------ |
| POST | api/auth/register | Registrar un nuevo usuario. |  |
| POST | api/auth/login | Obtener un JWT a través de las credenciales dadas. |  |
| POST | api/customers/create | Registra un nuevo cliente. | Authorization: Bearer eyJhbGciOiJIUzI1NiI... |
| POST | api/customers/get | Retorna el contenido de un cliente en especifico. | Authorization: Bearer eyJhbGciOiJIUzI1NiI... |
| POST | api/customers/delete | Elimina un cliente. | Authorization: Bearer eyJhbGciOiJIUzI1NiI... |

> Nota: Se adjunta el archivo `GDALab.postman_collection.json` para probar los servicios.
