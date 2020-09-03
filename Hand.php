<?php

class Hand
{
    public int $numCards;
    public array $cards = [];

    public function getCard(int $index): Card
    {
        $this->numCards--;
        return array_splice($this->cards, $index, 1)[0];
    }

    public function addCard(Card $card): void
    {
        $this->cards[] = $card;
        $this->numCards++;
    }

    public function sort(): void
    {
        usort($this->cards, function ($a, $b) {
            /**
             * @var Card $a
             * @var Card $b
             */
            if ($a->getRank() === $b->getRank()) {
                return 0;
            }
            return $a->getRank() > $b->getRank() ? 1 : -1;
        });
    }
}
