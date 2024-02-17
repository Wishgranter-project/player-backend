<?php

namespace AdinanCenci\Player\Controller;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;
use AdinanCenci\DescriptiveManager\PlaylistManager;
use AdinanCenci\Player\Helper\JsonResource;
use AdinanCenci\Player\Service\ServicesManager;

class ArtistsList extends ControllerBase
{
    /**
     * @var AdinanCenci\DescriptiveManager\PlaylistManager
     *   The service to manage playlists.
     */
    protected PlaylistManager $playlistManager;

    /**
     * @param AdinanCenci\DescriptiveManager\PlaylistManager $playlistManager
     */
    public function __construct(PlaylistManager $playlistManager)
    {
        $this->playlistManager = $playlistManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function instantiate(ServicesManager $servicesManager): ControllerBase
    {
        return new self($servicesManager->get('playlistManager'));
    }

    /**
     * {@inheritdoc}
     */
    public function generateResponse(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $artists  = $this->listArtists($request);
        $resource = new JsonResource();

        return $resource
            ->setStatusCode(200)
            ->setData($artists)
            ->renderResponse();
    }

    protected function listArtists(ServerRequestInterface $request): array
    {
        $list = [];

        foreach ($this->playlistManager->getAllPlaylists() as $playlistId => $playlist) {
            foreach ($playlist->items as $key => $item) {
                if (! $item->artist) {
                    continue;
                }
                $this->countArtists((array) $item->artist, $list);
            }
        }

        krsort($list);
        arsort($list);

        return $list;
    }

    protected function countArtists(array $itemArtists, &$list): void
    {
        foreach ($itemArtists as $a) {
            if (!isset($list[$a])) {
                $list[$a] = 1;
            } else {
                $list[$a]++;
            }
        }
    }
}
