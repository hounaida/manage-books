<?php

namespace App\Tests\Controller\Functional\Admin;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class BookTest extends WebTestCase
{
    private $client = null;
    private $session;
    private $userRepository;
    private $bookRepository;

    protected function setUp(): void
    {
        $this->client = static::createClient();

        self::bootKernel();

        $this->session = self::$container->get('session');

        $this->userRepository = self::$container->get(UserRepository::class);
        $this->bookRepository = self::$container->get(BookRepository::class);
    }

    public function testIndex()
    {
        $this->logIn();

        $this->client->request('GET', 'admin/book/');
        $response = $this->client->getResponse();

        $this->assertResponseStatusCodeSame(200);
        $this->assertStringContainsString('book 5', $response->getContent());
    }

    public function testShowBook()
    {
        $this->logIn();

        $book = $this->getBook();
        $this->client->request('GET', '/admin/book/'.$book->getId());
        $this->assertResponseStatusCodeSame(200);

        $response = $this->client->getResponse();
        $this->assertStringContainsString($book->getTitle(), $response->getContent());
        $this->assertStringContainsString($book->getCover(), $response->getContent());
        $this->assertStringContainsString($book->getDescritpion(), $response->getContent());
    }

    public function testNewBook()
    {
        $this->logIn();

        $crawler = $this->client->request('GET', 'admin/book/new');
        $form = $crawler->selectButton('Save')->form();
        $form['book[title]']->setValue('Test Book title 11');
        $form['book[descritpion]']->setValue('Test Book 11');
        $form['book[status]']->untick();
        $form['book[author]']->setValue('Foo Bar');

        $coverPath = __DIR__.'/../../../Images/ibiza-bohemia.jpg';
        $form['book[cover]']->upload($coverPath);

        $this->client->submit($form);
        $this->assertResponseStatusCodeSame(302);

        $book = $this->getBook('Test Book title 11');

        $this->assertNotNull($book);
        $this->assertSame($book->getTitle(), 'Test Book title 11');
        $this->assertSame($book->getDescritpion(), 'Test Book 11');
        $this->assertSame($book->getAuthor(), 'Foo Bar');
        $this->assertFalse($book->getStatus());

        $this->assertResponseRedirects('/admin/book/');
    }

    public function testEditBook()
    {
        $this->logIn();

        $book = $this->getBook();
        $crawler = $this->client->request('GET', 'admin/book/'.$book->getId().'/edit');
        $form = $crawler->selectButton('Update')->form();
        $form['book[title]']->setValue('New Book');

        $this->client->submit($form);

        $this->assertResponseStatusCodeSame(302);
        $this->assertResponseRedirects('/admin/book/');
    }

    /**
     * @dataProvider deleteBookProvider
     */
    public function testDelete(bool $status)
    {
        $this->logIn();

        $book = $this->getBookByStatus($status);
        $crawler = $this->client->request('GET', 'admin/book/'.$book->getId());

        if (!$status) {
            $form = $crawler->selectButton('Delete')->form();
            $this->client->submit($form);

            $this->assertResponseStatusCodeSame(302);
            $book = $this->getBook($book->getTitle());
            $this->assertNull($book);
        }
        else {
            $this->assertStringNotContainsString(
                '<button class="btn">Delete</button>',
                $this->client->getResponse()->getContent()
            );
        }
    }

    public function deleteBookProvider(): \Generator
    {
        yield [false];
        yield [true];
    }

    private function logIn()
    {
        // somehow fetch the user (e.g. using the user repository)
        $user = $this->userRepository->findOneByUsername('hounaida');

        $firewallName = 'main';

        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken($user, null, $firewallName, $user->getRoles());
        $this->session->set('_security_'.$firewallName, serialize($token));
        $this->session->save();

        $cookie = new Cookie($this->session->getName(), $this->session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function getBook(string $title = 'book 6'): ?Book
    {
        return $this->bookRepository->findOneByTitle($title);
    }

    private function getBookByStatus(Bool $status): ?Book
    {
        return $this->bookRepository->findOneByStatus($status);
    }
}