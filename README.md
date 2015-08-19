# osCommerce - Mercadopago Module (v2.2 - 2.3)
---
*Available for Argentina, Brazil, Mexico and Venezuela*
*Prueba de modificación"

## Installation:

1. Download Mercadopago module:
    * osCommerce 2.2
    * osCommerce 2.3

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

6. [NEW] Choose the category on the list that more describe your shop activities.

	![Category Selection](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/CategorySelection.png)

7. In the next screen, you will see **Mercadopago** listed as a payment method. Now, click on **Edit** on the right bar.
 
	![Payment Method List](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/PaymentMethodList.png)

8. Now, is very important to set your **CLIENT_ID** and **CLIENT_SECRET**, and choose the maximum number of installments that you want to accept in your store (the default is 24).
	
	Get your **CLIENT_ID** and **CLIENT_SECRET** in the following address:
	* Argentina: [https://www.mercadopago.com/mla/herramientas/aplicaciones](https://www.mercadopago.com/mla/herramientas/aplicaciones)
	* Brasil: [https://www.mercadopago.com/mlb/ferramentas/aplicacoes](https://www.mercadopago.com/mlb/ferramentas/aplicacoes)
	* Mexico: [https://www.mercadopago.com/mlm/herramientas/aplicaciones](https://www.mercadopago.com/mlm/herramientas/aplicaciones)
	* Venezuela: [https://www.mercadopago.com/mlv/herramientas/aplicaciones](https://www.mercadopago.com/mlv/herramientas/aplicaciones)

	Other options have been added and you can choose any time after installation:

	- **Sandbox:** By default, sandbox have been selected. We belive, you need a environment to test your first payment.

	- **Kind of Checkout:** Now you can choose what kind of checkout has adapted better with your front-end design, we recommend checkout transparent.


9. **DO NOT TOUCH** the fields *Country*, *Exclude Methods*, *Cod Status (fields…)*. They were generated for you with the correct values, if you need to change them, is highly recommended that you reinstall the module.

	![Do Not Touch](https://raw.github.com/mercadopago/cart-oscommerce/master/README.img/DoNotTouch.png)

10. The fields **Sucess Url** and **Pending url** were also generated automatically, but if you're testing in a localhost, it will not work. You can change the address to any of your choice, but can't be localhost.

11. Save your configuration and it's done!!

***IMPORTANT:***
*This module will only work with the following currencies:*

* Brazil:
	* BRL (Real)
	* USD (Dollar)
* Argentina:
	* ARS (Peso)
* México:
	* MXN  (Peso mexicano)
* Venezuela:
	* VEB (Peso venezuelano)

---
## Sync your backoffice with Mercadopago (IPN) 

1. Go to **Mercadopago IPN configuration**:
	* Argentina: [https://www.mercadopago.com/mla/herramientas/notificaciones](https://www.mercadopago.com/mla/herramientas/notificaciones)
	* Brazil: [https://www.mercadopago.com/mlb/ferramentas/notificacoes](https://www.mercadopago.com/mlb/ferramentas/notificacoes)
	* Mexico: [https://www.mercadopago.com/mlm/herramientas/notificaciones](https://www.mercadopago.com/mlm/herramientas/notificaciones)
	* Venezuela: [https://www.mercadopago.com/mlv/herramientas/notificaciones](https://www.mercadopago.com/mlv/herramientas/notificaciones)


2. Enter the URL as follow: ***[yourstoreaddress.com]***/mercadopago.php
