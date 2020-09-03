<?php

class Player
{
    const MAX_CARDS = 6;

    public string $name;
    private Hand $hand; // карты на руке

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->hand = new Hand();
    }

    public function canTakeCard(): bool
    {
        if ($this->hand->numCards >= self::MAX_CARDS) {
            $this->hand->sort();
            return false;
        }
        return true;
    }

    public function takeCard(Card $card): void
    {
        echo "(deck) {$this->name} <-- {$card->getTypeName()}{$card->getSuitName()}\n";
        $this->hand->addCard($card);
    }

    public function takeLosingCards(array $cards): void
    {
        foreach ($cards as $card) {
            $this->hand->addCard($card);
        }
        $this->hand->sort();
    }

    public function attack(array $inGameCards): ?Card
    {
        if (empty($inGameCards)) { // если разыгранных карт нет, то атакуем первой имеющейся
            return $this->hand->getCard(0);
        }
        /** @var Card $hCard */
        $cards = $this->hand->cards;
        foreach ($cards as $k => $hCard) {
            /** @var Card $igCard */
            foreach ($inGameCards as $igCard) {

                if ( ($igCard->getType() === $hCard->getType() && !$hCard->isTrump()) ||
                    (
                        $igCard->getType() === $hCard->getType() &&
                        $hCard->isTrump() &&
                        ($this->hand->numCards === 1 || $hCard->getRank() != $cards[array_key_last($cards)]->getRank())
                    )
                ) {
                    return $this->hand->getCard($k);
                }
            }
        }

        return null;
    }

    public function hasCards(): bool
    {
        return $this->hand->numCards > 0;
    }

    public function defend(Card $attackCard): ?Card
    {
        $cards = $this->hand->cards;
        /** @var Card $card */
        foreach ($cards as $k => $card) {
            // 2 проверка на козыря: карты сортированы и если мы уже дошли до козырей, значит нет карты для дефа
            if (
                ($card->getSuit() === $attackCard->getSuit() && $card->getType() > $attackCard->getType()) ||
                ($card->isTrump() && !$attackCard->isTrump())
            ) {
                return $this->hand->getCard($k);
            }
        }

        return null;
    }

    public function discardSimilar(array $inGameCards): array
    {
        $discards = [];

        if (empty($inGameCards)) {
            return $discards;
        }

        $cards = $this->hand->cards;
        /** @var Card $card */
        foreach ($cards as $k => $card) {
            /** @var Card $igCard */
            foreach ($inGameCards as $igCard) {
                if (!$card->isTrump() && ($card->getType() === $igCard->getType())) {
                    $discards[] = $this->hand->getCard($k);
                    break;
                }
            }
        }

        return $discards;
    }


    public function writeLog(): void
    {
        echo "{$this->name} ({$this->hand->numCards}): ( ";
        /** @var Card $card */
        foreach ($this->hand->cards as $card) {
            echo "{$card->getTypeName()}{$card->getSuitName()} ";
        }
        echo ")";
    }



}
