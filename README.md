# PRUEBA TECNICA BACKEND IDBI

Este proyecto es una solución al reto técnico para el puesto de Desarrollador Backend en IDBI. La aplicación es una API desarrollada en Laravel para gestionar comprobantes en formato XML. Incluye funcionalidades como registro, consulta, eliminación y generación de estadísticas relacionadas con los comprobantes.


## **Requisitos**
Asegúrate de haber seguidos los pasos del [README2.md](https://github.com/ShandeAlexis/PRUEBAA_BACKEND/blob/main/README2.md) para levantar el proyecto:

**Adjunto este video "Audiovisual" para que se pueda comprender mejor.** 

[Audiovisual](https://drive.google.com/file/d/1Z0o8DQueFy4fd1AMonFNZQMGcwzE59E3/view?usp=sharing)


## **Funcionalidades**
1.  **Registro de Comprobantes**
    
    -   Subida de archivos XML.
    -   Procesamiento asíncrono.
    -   Resumen por correo al completar la carga.
2.  **Consulta de Comprobantes**:
    
    -   Detalles de cada comprobante.
    -   Filtros avanzados (serie, número, tipo, moneda, rango de fechas).
3. **Estadísticas**:
    
    -   Totales acumulados por moneda.
4. **Eliminación de Comprobantes**:
    
    -   Elimina comprobantes registrados por el usuario autenticado.


### Procesamiento de Tareas Asíncronas en un Entorno Docker con Laravel 

1.  Ejecuta el siguiente comando para ingresar al contenedor Docker donde está corriendo el servicio web:

    `docker exec -it idbi-invoice-recorder-challenge-web-1 bash` 
   
    
2.  Dentro del contenedor, ejecutas el comando para procesar trabajos pendientes.
       `php artisan queue:work`


### **Decisiones Técnicas**

-   Uso de colas de trabajo para el procesamiento asíncrono.
-   Validaciones en middleware para autorización.
-   Optimización de consultas SQL para filtrar comprobantes.

### **Endpoints**
`POST http://localhost:8080/api/v1/users` : Se encargar de registrar los usuarios.
`POST http://localhost:8080/api/v1/login` : Se logea en la aplicación y devuelve un token de acceso.
`GET http://localhost:8080/api/v1/vouchers?start_date=2024-12-07&end_date=2024-12-08&serie=F003-1` : Lista los comprobantes pasados mediante los filtros del parámetro. 
`POST http://localhost:8080/api/v1/vouchers` : Se registra uno o varios comprobantes.
`POST http://localhost:8080/api/v1/vouchers/voucher/total-amounts/` : Se calcula la suma de los comprobantes en PEN Y USD del usuario . 
`DELETE http://localhost:8080/api/v1/vouchers/{id}` : Se elimina el comprobante mediante su id.


![chems thanks](https://i.pinimg.com/1200x/98/2e/a9/982ea98481fcbdf3281e699eb424fafd.jpg)
