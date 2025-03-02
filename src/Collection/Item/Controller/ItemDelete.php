<?php

namespace WishgranterProject\Backend\Collection\Item\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use WishgranterProject\Backend\Collection\Controller\CollectionController;
use WishgranterProject\Backend\Exception\NotFound;
use WishgranterProject\Backend\Helper\JsonResource;

/**
 * Deletes an item from the collection.
 */
class ItemDelete extends CollectionController
{
    /**
     * {@inheritdoc}
     */
    public function __invoke(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {

        $uuid = $request->getAttribute('itemUuid');
        if (!$item = $this->playlistManager->findItemByUuid($uuid, $playlistId)) {
            throw new NotFound('Item ' . $uuid . ' does not exist.');
        }

        $playlist = $this->playlistManager->getPlaylist($playlistId);
        $playlist->deleteItem($item);
        $data = $this->describer->describe($item);

        $resource = new JsonResource();
        return $resource
            ->addSuccess(200, 'Item deleted')
            ->renderResponse();
    }
}
