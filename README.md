# osCommerce - Mercadopago Module (v2.2 - 2.3)
---

## Installation:

1. Download Mercadopago module:
    * osCommerce 2.2
    * osCommerce 2.3<br />

2. Copy the module folder to your osCommerce ROOT installation.

---
## Setup Mercadopago

1. On your store administration, go to **Modules > Payment**.

2. Click **Install Module**.

3. Click on **Mercadopago** then in **+Install Module**.

4. Choose your country:

	![Country Selecion](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/CountrySelection.png)

5. Choose the payment methods that you don´t want to accept in your Store:

	![Payment Methods Selection](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/PaymentMethodsSelection.png)

6. In the next screen, you will see **Mercadopago** listed as a payment method. Now, click on **Edit** on the right bar.
 
	![Payment Method List](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/PaymentMethodList.png)

7. Now, is very important to set your **CLIENT_ID** and **CLIENT_SECRET**, and choose the maximum number of installments that you want to accept in your store (the default is 18).
	
	Get your **CLIENT_ID** and **CLIENT_SECRET** in the following address:
	* Argentina: [https://www.mercadopago.com/mla/herramientas/aplicaciones](https://www.mercadopago.com/mla/herramientas/aplicaciones)
	* Brazil: [https://www.mercadopago.com/mlb/ferramentas/aplicacoes](https://www.mercadopago.com/mlb/ferramentas/aplicacoes)<br />

8. **DO NOT TOUCH** the fields *Country*, *Exclude Methods*, *Cod Status (fields…)*. They were generated for you with the correct values, if you need to change them, is highly recommended that you reinstall the module.

	![Do Not Touch](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/DoNotTouch.png)

9. The fields **Sucess Url** and **Pending url** were also generated automatically, but if you're testing in a localhost, it will not work. You can change the address to any of your choice, but can't be localhost.

10. Save your configuration and it's done!!

***IMPORTANT:***
*This module will only work with the following currencies:*

* Brasil:
	* BRL (Real)
	* USD (Dollar)
* Argentina:
	* ARS (Peso)

---
## Sync your backoffice with Mercadopago (IPN) 

1. Go to **Mercadopago IPN configuration**:
	* Argentina: [https://www.mercadopago.com/mla/herramientas/notificaciones](https://www.mercadopago.com/mla/herramientas/notificaciones)
	* Brasil: [https://www.mercadopago.com/mlb/ferramentas/notificacoes](https://www.mercadopago.com/mlb/ferramentas/notificacoes)

2. Enter the URL as follow: ***[yourstoreaddress.com]***/mercadopago.php
