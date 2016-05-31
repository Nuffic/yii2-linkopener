<?php

namespace nuffic\linkopener;

class Message extends \yii\swiftmailer\Message
{
    public function getTextBody()
    {
        if ($this->swiftMessage->getBody() === null) {
            return (string) $this->getPart('text/plain');
        }

        return (string) $this->swiftMessage->getBody();
    }

    public function getHtmlBody()
    {
        if ($this->swiftMessage->getBody()!==null) {
            return null;
        }
        return (string) $this->getPart('text/html');
    }

    private function getPart($type)
    {
        return array_reduce($this->getSwiftMessage()->getChildren(), function ($carry, $item) use ($type) {
            /** @var  */
            if ($item->getContentType() == $type) {
                return $item;
            }
            return $carry;
        });
    }
}
