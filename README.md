[![Build Status](https://travis-ci.org/ozean12/GooglePubSubBundle.svg?branch=master)](https://travis-ci.org/ozean12/GooglePubSubBundle)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ozean12/GooglePubSubBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ozean12/GooglePubSubBundle/?branch=master)
[![Latest Stable Version](https://poser.pugx.org/ozean12/googlepubsub/v/stable.png)](https://packagist.org/packages/ozean12/googlepusubscriptionsl Downloads](https://poser.pugx.org/ozean12/googlepubsub/downloads.png)](https://packagist.org/packages/ozean12/googlepubsub)

# Ozean12GooglePubSubBundle
A Symfony 2 / Symfony 3 bundle which integrates [Google Cloud Pub Sub](https://cloud.google.com/pubsub/docs/overview) with your application.
## Installation
##### 1. Require the bundle:
```bash
composer require ozean12/googlepubsub
```
##### 2. Set it up:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = [
        // ...
        new JMS\SerializerBundle\JMSSerializerBundle(), // if not already enabled
        new Ozean12\WebTranslateItBundle\Ozean12WebTranslateItBundle(),
    ];
    
    // ...
}
```

```yaml
# app/config/config.yml

ozean12_google_pub_sub:
  # your Google Cloud Project ID
  project_id: "%google_pub_sub_project_id%"
  
  # path to your Google Cloud Application Credentials file
  key_file_path: "%google_pub_sub_key_file_path%"
  
  # add this if you want to use logger (see Using Logger section for more info)
  logger_channel: pub_sub
  
  # list of PubSub topics (See Publishing section for more info)
  topics:
    - my_topic
    
  # list of Push subscriptions in format (subscription_name: service_name)
  push_subscriptions:
    my_push_subscription: my_bundle.my_firts_push.subscriber

```
## Usage
### 1. Publishing messages
In order to publish the message to PubSub, you need to define the topic first, as described in previous section.
Once topic is defined, it can be accessed within the Publisher service:

```php
$message = new MyTopicMessage();
$publisher = $this->container->get('ozean12_google_pubsub.publisher.my_topic');
$result = $publisher->publish($message);
```

`MyTopicMessage` is a simple class which implements the `Ozean12\GooglePubSubBundle\DTO\MessageDataDTOInterface` interface and holds the data which needs to be included in message.

`publish` method also accepts `attributes` array as a second argument.
 
If topic does not exist, it will be created automatically.
 
Result of the call will be an instance of `Ozean12\GooglePubSubBundle\DTO\PublishMessageResultDTO` which will contain the ids of created messages.
### 2. Subscribing to Push messages
When using the [Push subscription](https://cloud.google.com/pubsub/docs/subscriber#overview-of-subscriptions), you need to create the endpoint which will receive the message from Google Pub/Sub and then pass it to `PushSubscriberManager`
An example of this setup using the [FOS REST Bundle](http://symfony.com/doc/current/bundles/FOSRestBundle/index.html):
```php
// GoogleCloudController.php

use Ozean12\GooglePubSubBundle\DTO\PushMessageRequestDTO;

/**
 * Class GoogleCloudController
 *
 * @Version("v1")
 * @Route("/google-cloud/")
 */
class GoogleCloudController extends FOSRestController
{
    /**
     * @ApiDoc(
     *   resource = true,
     *   description = "Receive a new PubSub message and process it",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     403 = "Returned when access denied"
     *   }
     * )
     *
     * @ParamConverter("message", converter="fos_rest.request_body")
     *
     * @Route("pub-sub")
     * @Method({"POST"})
     * @View()
     *
     * @param PushMessageRequestDTO $message
     * @return mixed
     */
    public function pubSub(PushMessageRequestDTO $message)
    {
        $this->get('ozean12_google_pubsub.push_subscriber_manager.service')->processMessage($message);
    }
}
```
By using the `@ParamConverter` annotation Symfony will automatically convert the request to `PushMessageRequestDTO`

`PushMessageRequestDTO` has two properties: 
- `message`: instance of `PushMessageDTO`. For all the fields refer to [Push request body](https://cloud.google.com/pubsub/docs/subscriber#receive)
- `subscription`: a string with the name of Pub/Sub subscription

The `PushSubscriberManager->processMessage()` method will iterate over all registered subscribers and run the one with the name equal to the subscription property of the message. Example of subscriber:
```php
// MyPushSubscriber.php

use Ozean12\GooglePubSubBundle\DTO\PushMessageDTO;
use Ozean12\GooglePubSubBundle\Service\Subscriber\PushSubscriberInterface;

/**
 * Class MyPushSubscriber
 */
class MyPushSubscriber implements PushSubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(PushMessageDTO $message)
    {
        /** @var SendTransactionalEmailDataDTO $emailDTO */
        $data = base64_decode($message->getData())

        // process your data here
    }
}
```
### 3. Using Logger
If you want to log the interaction with Google services, setup the `logger_channel` option with the channel you want to use. Ex:
```yaml
monolog:
  handlers:
    # ...
    pub_sub:
      type:  stream
      path:  "%kernel.logs_dir%/pub_sub_%kernel.environment%.log"
      level: info
      channels: [pub_sub]
  channels:
    # ...
    - pub_sub
    
ozean12_google_pub_sub:
  # ...
  logger_channel: pub_sub
```
This will produce following log entries:
```text
[2016-12-23 17:51:42] pub_sub.INFO: New topic my_topic created {"topic":"my_topic"} []
[2016-12-23 17:57:01] pub_sub.INFO: Message(s) 123456789 submitted to topic my_topic {"messages":"123456789","topic":"my_topic"} []
```

## Credits
[Ozean12](http://ozean12.com)
