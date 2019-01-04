<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CategoryControllerTest extends WebTestCase
{
    public function testShow()
  {
    // get the custom parameters from app config.yml
    $kernel = static::createKernel();
    $kernel->boot();
    $max_jobs_on_homepage = $kernel->getContainer()->getParameter('max_jobs_on_homepage');
    $max_jobs_on_category = $kernel->getContainer()->getParameter('max_jobs_on_category');
 
    $client = static::createClient();
 
    // categories on homepage are clickable
    $crawler = $client->request('GET', '/');
    $link = $crawler->selectLink('Programming')->link();
    $crawler = $client->click($link);
    $this->assertEquals('AppBundle\Controller\CategoryController::showAction', $client->getRequest()->attributes->get('_controller'));
    $this->assertEquals('programming', $client->getRequest()->attributes->get('slug'));
 
    // categories with more than $max_jobs_on_homepage jobs also have a "more" link
    $crawler = $client->request('GET', '/');
    $link = $crawler->selectLink('22')->link();
    $crawler = $client->click($link);
    $this->assertEquals('AppBundle\Controller\CategoryController::showAction', $client->getRequest()->attributes->get('_controller'));
    $this->assertEquals('programming', $client->getRequest()->attributes->get('slug'));
 
    // only $max_jobs_on_category jobs are listed
    $this->assertTrue($crawler->filter('.jobs tr')->count() <= $max_jobs_on_category);
    $this->assertRegExp('/32 jobs/', $crawler->filter('.pagination_desc')->text());
    $this->assertRegExp('/page 1\/2/', $crawler->filter('.pagination_desc')->text());
 
    $link = $crawler->selectLink('2')->link();
    $crawler = $client->click($link);
    $this->assertEquals(2, $client->getRequest()->attributes->get('page'));
    $this->assertRegExp('/page 2\/2/', $crawler->filter('.pagination_desc')->text());
  }
    /*
    public function testCompleteScenario()
    {
        // Create a new client to browse the application
        $client = static::createClient();

        // Create a new entry in the database
        $crawler = $client->request('GET', '/category/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode(), "Unexpected HTTP status code for GET /category/");
        $crawler = $client->click($crawler->selectLink('Create a new entry')->link());

        // Fill in the form and submit it
        $form = $crawler->selectButton('Create')->form(array(
            'appbundle_category[field_name]'  => 'Test',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check data in the show view
        $this->assertGreaterThan(0, $crawler->filter('td:contains("Test")')->count(), 'Missing element td:contains("Test")');

        // Edit the entity
        $crawler = $client->click($crawler->selectLink('Edit')->link());

        $form = $crawler->selectButton('Update')->form(array(
            'appbundle_category[field_name]'  => 'Foo',
            // ... other fields to fill
        ));

        $client->submit($form);
        $crawler = $client->followRedirect();

        // Check the element contains an attribute with value equals "Foo"
        $this->assertGreaterThan(0, $crawler->filter('[value="Foo"]')->count(), 'Missing element [value="Foo"]');

        // Delete the entity
        $client->submit($crawler->selectButton('Delete')->form());
        $crawler = $client->followRedirect();

        // Check the entity has been delete on the list
        $this->assertNotRegExp('/Foo/', $client->getResponse()->getContent());
    }

    */
}
