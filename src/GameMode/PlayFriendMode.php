<?php

namespace ChessServer\GameMode;

class PlayFriendMode extends AbstractMode
{
    const NAME = 'playfriend';

    protected $jwt;

    public function __construct(Game $game, array $resourceIds, string $jwt)
    {
        parent::__construct($game, $resourceIds);

        $this->jwt = $jwt;
        $this->hash = md5($jwt);
    }

    public function getJwt()
    {
        return $this->jwt;
    }
}
