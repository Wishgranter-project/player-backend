<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Service\ServicesManager;
use AdinanCenci\Player\Service\Describer;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Exception\NotFound;

class ItemRead extends ControllerBase
{
    /**
     * @var AdinanCenci\DescriptiveManager\PlaylistManager
     */
    protected PlaylistManager $playlistManager;

    /**
     * @var AdinanCenci\Player\Service\Describer
     */
    protected Describer $describer;

    /**
     * @param AdinanCenci\DescriptiveManager\PlaylistManager $playlistManager
     * @param AdinanCenci\Player\Service\Describer $describer
     */
    public function __construct(PlaylistManager $playlistManager, Describer $describer)
    {
        $this->playlistManager = $playlistManager;
        $this->describer       = $describer;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self(
            $servicesManager->get('playlistManager'),
            $servicesManager->get('describer')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $uuid = $request->getAttribute('itemUuid');

        if (!$item = $this->playlistManager->getItemByUuid($uuid)) {
            throw new NotFound('Item ' . $playlistId . ' does not exist.');
        }

        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->setData($data)
            ->renderResponse();
    }
}
