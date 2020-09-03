<?php


class Card
{
    const SUIT_SPADES = 0;
    const SUIT_HEART = 1;
    const SUIT_CLUBS = 2;
    const SUIT_DIAMONDS = 3;

    const CARD_TYPES = [
        '6', '7', '8', '9', '10', 'В', 'Д', 'К', 'Т'
    ];
    const CARD_SUITS = [
        self::SUIT_SPADES => '♠',
        self::SUIT_HEART => '♥',
        self::SUIT_CLUBS => '♣',
        self::SUIT_DIAMONDS => '♦'
    ];

    // порядок отличается от изначальной колоды.
    const CARDS_SUIT_SORT_RATE = [
        self::SUIT_SPADES => 1,//'♠',
        self::SUIT_CLUBS => 2,//'♣',
        self::SUIT_DIAMONDS => 3,//'♦',
        self::SUIT_HEART => 4,//'♥',
    ];

    const TRUMP_COEFFICIENT = 100;

    private int $suit;
    private int $type;
    private bool $isTrump;
    private int $rank;

    public function __construct(int $suit, int $type, bool $isTrump)
    {
        $this->suit = $suit;
        $this->type = $type;
        $this->isTrump = $isTrump;

        $this->setRankCard();
    }

    public function setIsTrumpTrue(): void
    {
        $this->rank += self::TRUMP_COEFFICIENT;
        $this->isTrump = true;
    }

    public function isTrump(): bool
    {
        return $this->isTrump;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function getSuit(): int
    {
        return $this->suit;
    }

    public function getRank(): int
    {
        return $this->rank;
    }

    public function getSuitName(): string
    {
        return self::CARD_SUITS[$this->suit];
    }

    public function getTypeName(): string
    {
        return self::CARD_TYPES[$this->type];
    }

    private function setRankCard(): void
    {
        $this->rank = self::CARDS_SUIT_SORT_RATE[$this->suit] + $this->type * 10;
    }
}
