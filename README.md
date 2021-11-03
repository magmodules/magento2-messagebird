<br>
<p align="center">
  <img width="400" src="https://user-images.githubusercontent.com/24823946/139876441-caab98d5-2f65-46f9-baa4-002a10179f31.png">
</p>
<br>


## MessageBird Connect for Magento® 2

The MessageBird Connect extension makes it effortless to connect your Magento® 2 catalog with the MessageBird platform.  
Support for Magento 2.3.3 and higher.

## Installation

To make the integration process as easy as possible for you, we have developed various plugins for your webshop software package. Before you start up the installation process, we recommend that you make a backup of your webshop files, as well as the database.

### **Install by using Composer**

Magento® 2 uses the Composer to manage the module package and the library. Composer is a dependency manager for PHP. Composer declares the libraries your project depends on and it will manage (install/update) them for you.

Check if your server has composer installed by running the following command:

`composer –v`

If your server doesn’t have composer installed, you can easily install it by using this manual: https://getcomposer.org/doc/00-intro.md

Step-by-step to install the Magento® 2 extension through Composer:

1\. Connect to your server running Magento® 2 using SSH or another method (make sure you have access to the command line).  
2\. Locate your Magento® 2 project root.  
3\. Install the Magento® 2 extension through composer and wait till it's completed:

`composer require magmodules/magento2-messagebird`

4\. Once completed run the Magento® module enable command:

`bin/magento module:enable Magmodules_MessageBird`

5\. After that run the Magento® upgrade and clean the caches:

`php bin/magento setup:upgrade`  
`php bin/magento cache:flush`

6\.  If Magento® is running in production mode you also need to redeploy the static content:

`php bin/magento setup:static-content:deploy`

7\.  After the installation: Go to your Magento® admin portal and open ‘Stores’ > ‘Configuration’ > ‘Magmodules’ > ‘Channable’.  

## Development by Magmodules

We are a Dutch Magento® Only Agency dedicated to the development of extensions for Magento® 1 and Magento® 2. All our extensions are coded by our own team and our support team is always there to help you out.

[Visit Magmodules](https://www.magmodules.eu/)

## Developed for Messagebird

MessageBird is a cloud communications platform that enables consumers in virtually every corner of the planet to connect with businesses in the same way they connect with their friends - seamlessly, on their own timeline, and with context. MessageBird is amongst the leading Service Providers in the CPaaS market

[Visit Messagebird](https://www.messagebird.com/nl/)

## Use together with Mollie Payment and unlock great features!

The plugin is developed in collaboration with [Mollie payments](https://www.mollie.com/), we have made it possible to notify customers about unfinished payments using an e-mail. With the MessageBird integration, we can share this payment link directly with them through a text message so the customers can directly revive their order.

**Please note that;**  
\- This feature is only available when the [Mollie Payments plugin](https://github.com/mollie/magento2) is installed and enabled;  
\- The message is only sent if the customer didn't finish their order in a certain time period.
