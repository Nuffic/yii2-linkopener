<?php

namespace nuffic\linkopener;

use GuzzleHttp\Client;
use yii\mail\BaseMailer;

/**
 * Class Mailer
 *
 * @package nuffic\linkopener
 *
 * @property Client $client
 */
class Mailer extends BaseMailer
{
    private $_client;

    /**
     * @param \yii\mail\BaseMessage $message
     *
     * @return boolean
     */
    public function sendMessage($message)
    {
        return $this->processHtml($message->htmlBody) && $this->processText($message->textBody);
    }

    private function processHtml($html)
    {
        $qp = htmlqp($html);
        $links = $qp->find('a');
        foreach ($links as $link) {
            if (!$this->visitLink($link->attr('href'))) {
                return false;
            }
        }

        return true;
    }

    private function processText($string)
    {
        preg_match_all('@\b(?<url>https?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/)))@', $string, $matches);
        foreach ($matches as $match) {
            if (!$this->visitLink($match['url'])) {
                return false;
            }
        }
        return true;
    }

    private function visitLink($link)
    {
        try {
            $this->client->get($link);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @return Client
     */
    private function getClient()
    {
        if (!$this->_client) {
            $this->_client = new Client();
        }
        return $this->_client;
    }
}
