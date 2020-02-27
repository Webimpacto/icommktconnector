# icommktconnector
ICOMMKT PrestaShop Connector

# Abandoment Cart

## Definición

Permite enviar a ICOMMKT los carritos abandonados que hay en la web para poder enviar un correo a los usuarios propietarios de dichos carritos. En el email enviado a los clientes se incluye una URL que permite cargar el carrito abandonado con los productos que contenía

Se tienen en cuenta los siguiente puntos a la hora de obtener los carritos:

- Sólo se obtendrán los carritos abandonados que tengan un email asociado, es decir, los carritos de los usuarios registrados.
- Solamente se recibirán los carritos que no tengan un pedido asociado.

## Campos configurables

Se han añadido los siguientes campos al módulo "icommktconnector" para controlar el envío de carritos abandonados:
- **Api key**
    - Código de la cuenta. Es el código obtenido de la cuenta de ICOMMKT.
- **Profile key Abandon**
    - Código del perfil. Es el código obtenido del perfil de ICOMMKT donde se van a enviar los carritos abandonados.
- **Secure Token**
    - Token de seguridad. Es el mismo que se debe introducir en la URL. Si ambos token no coinciden las funciones no se ejecutarán.
- **Days to abandon**
    - Días para que un carrito se considere abandonado. Por defecto un carrito se considera abandonado tras un día.
- **Friendly URL**
    - Permite activar o desactivar las URLs amigables del módulo

## Acciones disponibles

- **load_cart**
    - Permite cargar el carrito abandonado.
    - La estructura de la URL es la siguiente
        - http://prueba.net/abandomentcart?action=load_cart&secure_token=[secure_token]&id_cart=[id_cart]
        - http://prueba.net/index.php?controller=abandomentcart&module=icommktconnector&action=load_cart&secure_token=[secure_token]&id_cart=[id_cart]
- **sendAbandomentcarts**
    - Permite enviar los carritos abandonados obtenidos
    - Esta URL se utilizará como un cron que se debe ejecutar todos los días para tener los carritos abandonados actualizados.
     - La estructura de la URL es la siguiente
        - http://prueba.net/abandomentcart?action=sendAbandomentcarts&secure_token=[secure_token]
        - http://prueba.net/index.php?controller=abandomentcart&module=icommktconnector&action=sendAbandomentcarts&secure_token=[secure_token]

## Tablas creadas

Al instalar el módulo se crean las siguientes tablas:

- **commktconnector_abandomentcarts**
    - En esta tabla se almacenan los carritos que han sido enviados a ICOMMKT correctamente.
- **commktconnector_abandomentcarts_error**
    - Esta tabla se utiliza para almacenar los carritos que no se han podido enviar a ICOMMKT. Además cuenta con un campo "error" en el que se almacena el error que se ha generado al enviar el carrito.

## Importante tener en cuenta

Cuando se ejecuta el cron, se añaden a la tabla "commktconnector_abandomentcarts" los carritos que se han registrado correctamente en la base de datos de ICOMMKT. Es posible, que algunos usuarios tengan varios carritos que coincidan en la fecha configurada en "Days to abandon", por lo que es posible que si se ejecuta de nuevo la consulta para enviar los carritos, se envíe otro de los carritos asociados al cliente que tenía varios.

# Envio de usuarios a un perfil de Icommkt

## Definición

Permite enviar a ICOMMKT los usuarios que hay en la web.

Se tienen en cuenta los siguiente puntos a la hora de obtener los usuarios:

- Sólo se obtendrán los usuarios que se hayan registrado en la newsletter.

## Campos configurables

- **Api key**
    - Código de la cuenta. Es el código obtenido de la cuenta de ICOMMKT.
- **Profile key**
    - Código del perfil. Es el código obtenido del perfil de ICOMMKT.
- **Secure Token**
    - Token de seguridad. Es el mismo que se debe introducir en la URL. Si ambos token no coinciden las funciones no se ejecutarán.    

## Acciones disponibles

- Envio de los usuarios.
    - La estructura de la URL es la siguiente
        - http://prueba.net/.net/index.php?fc=module&controller=sendtoicommkt&module=icommktconnector&action=sendtoicommktuser&secure_token=[secure_token]

## Tablas modificadas

Al instalar el módulo se modificarán las siguientes tablas dependiendo de la versión de Prestashop:

- **emailsubscription**
    - Tabla para versiones de Prestashop > 1.7.
    - Se añadirán dos nuevas columnas: is_send_icommkt & date_send_icommkt.
- **newsletter**
    - Tabla para versiones de Prestashop < 1.7.
    - Se añadirán dos nuevas columnas: is_send_icommkt & date_send_icommkt.

## Importante tener en cuenta

Cuando se ejecuta el cron, se enviarán todos los usuarios a ICOMMKT. Si el usuario se ha registrado correctamente se actualizará la columna **is_send_icommkt**  modificando su valor a 1, evitando que se vuelva a enviar en un futuro.    