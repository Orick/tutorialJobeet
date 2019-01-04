<?php

namespace AppBundle\Entity;
use AppBundle\Utils\Jobeet;

/**
 * Category
 */
class Category
{
    /**
     * @var int
     */
    private $id;

    
    private $moreJobs;

    private $slug;

    /**
     * @var string
     */
    private $name;

    private $activeJobs;
    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setActiveJobs($jobs)
    {
        $this->activeJobs = $jobs;
    }
    public function getActiveJobs()
    {
        return $this->activeJobs;
    }


    public function setMoreJobs($jobs)
    {
        $this->moreJobs = $jobs >=  0 ? $jobs : 0;
    }
    
    public function getMoreJobs()
    {
        return $this->moreJobs;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->jobs = new \Doctrine\Common\Collections\ArrayCollection();
        $this->affiliates = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add job
     *
     * @param \AppBundle\Entity\Job $job
     *
     * @return Category
     */
    public function addJob(\AppBundle\Entity\Job $job)
    {
        $this->jobs[] = $job;

        return $this;
    }

    /**
     * Remove job
     *
     * @param \AppBundle\Entity\Job $job
     */
    public function removeJob(\AppBundle\Entity\Job $job)
    {
        $this->jobs->removeElement($job);
    }

    /**
     * Get jobs
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getJobs()
    {
        return $this->jobs;
    }

    /**
     * Add affiliate
     *
     * @param \AppBundle\Entity\Affiliate $affiliate
     *
     * @return Category
     */
    public function addAffiliate(\AppBundle\Entity\Affiliate $affiliate)
    {
        $this->affiliates[] = $affiliate;

        return $this;
    }

    /**
     * Remove affiliate
     *
     * @param \AppBundle\Entity\Affiliate $affiliate
     */
    public function removeAffiliate(\AppBundle\Entity\Affiliate $affiliate)
    {
        $this->affiliates->removeElement($affiliate);
    }

    /**
     * Get affiliates
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAffiliates()
    {
        return $this->affiliates;
    }

    public function getSlug(){
        return $this->slug;
    }


    public function setSlugValue()
    {
        $this->slug = Jobeet::slugify($this->getName());
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    public function __toString()
    {
        return $this->getName() ? $this->getName() : "";
    }

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $jobs;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $affiliates;


}
