<?php

class CardsDeck
{
    CONST FIRST_ROUND_DEAL_CARDS = 6;
    public array $pack = [];

    public Card $trumpCard;

    public function __construct(int $iterableNumSort)
    {
        echo "Deck num: {$iterableNumSort}\n";
        $this->generate();
        $this->shuffle($iterableNumSort);
    }

    private function generate(): void
    {
        $suits = count(Card::CARD_SUITS);
        $types = count(Card::CARD_TYPES);
        for ($i = 0; $i < $suits; $i++) {
            for ($j = 0; $j < $types; $j++) {
                $this->pack[] = new Card($i, $j, false);
            }
        }
    }

    private function shuffle(int $iterableNumSort): void
    {
        for ($i = 0; $i < 1000; $i++) {
            $n = ($iterableNumSort + $i * 2) % 36;
            $card = array_splice($this->pack, $n, 1)[0];
            array_unshift($this->pack, $card);
        }
    }

    private function setTrump(int $numPlayers): void
    {
        $this->trumpCard = $this->pack[$numPlayers * self::FIRST_ROUND_DEAL_CARDS];
        /** @var Card $item */
        foreach ($this->pack as $item) {
            if ($item->getSuit() === $this->trumpCard->getSuit()) {
                $item->setIsTrumpTrue();
            }
        }
    }

    public function dealCards(Player $player): void
    {
        $cardTaken = true;
        while ($cardTaken) {
            $cardTaken = $this->giveCard($player);
        }
    }

    public function firstDeal($players): void
    {
        $this->setTrump(count($players));
        foreach ($players as $player) {
            $this->dealCards($player);
        }
        array_shift($this->pack);
        array_push($this->pack, $this->trumpCard);
    }

    private function giveCard(Player $player): bool
    {
        if (!$player->canTakeCard() || empty($this->pack)) {
            return false;
        }
        $card = array_shift($this->pack);
        $player->takeCard($card);

        return true;
    }

    public function writeLog(): void
    {
        echo "Trump: ".$this->trumpCard->getTypeName().$this->trumpCard->getSuitName()."\n";
    }
}
