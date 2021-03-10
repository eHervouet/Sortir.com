<?php
namespace App\Entity;

use phpDocumentor\Reflection\Types\Boolean;

class PropertySearch {

    /**
     *@var Participants|null
     */
    private $organisateur;

    /**
     *@var bool|null
     */
    private $inscrit;

    /**
     * @return bool|null
     */
    public function getInscrit(): ?bool
    {
        return $this->inscrit;
    }

    /**
     * @return Participants|null
     */
    public function getOrganisateur(): ?Participants
    {
        return $this->organisateur;
    }

    /**
     * @param bool|null $inscrit
     */
    public function setInscrit(bool $inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @param Participants|null $organisateur
     */
    public function setOrganisateur(Participants $organisateur): void
    {
        $this->organisateur = $organisateur;
    }
}