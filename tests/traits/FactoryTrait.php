<?php

namespace App\Tests\traits;

use App\Entity\Action;
use App\Entity\ActionSource;
use App\Entity\BlogPost;
use App\Entity\Candidate;
use App\Entity\CandidateProblemOpinion;
use App\Entity\Category;
use App\Entity\Constituency;
use App\Entity\ConstituencyProblem;
use App\Entity\Election;
use App\Entity\Institution;
use App\Entity\InstitutionTitle;
use App\Entity\Mandate;
use App\Entity\Party;
use App\Entity\Politician;
use App\Entity\Power;
use App\Entity\Problem;
use App\Entity\Promise;
use App\Entity\PromiseSource;
use App\Entity\PromiseUpdate;
use App\Entity\Status;
use App\Entity\Title;
use Doctrine\Common\Persistence\ObjectManager;
use App\Tests\traits\UtilsTrait;

trait FactoryTrait
{
    use UtilsTrait;

    protected static function makeStatus(ObjectManager $em) : Status
    {
        $random = self::randomNumber();

        $status = new Status();
        $status->setColor('blue');
        $status->setEffect($random);
        $status->setName("Test ${random}");
        $status->setNamePlural("Tests ${random}");
        $status->setSlug("test-${random}");

        $em->persist($status);
        $em->flush($status);

        return $status;
    }

    protected static function makeInstitution(ObjectManager $em) : Institution
    {
        $random = self::randomNumber();

        $institution = new Institution();
        $institution->setName("Test ${random}");
        $institution->setSlug("test-${random}");

        $em->persist($institution);
        $em->flush($institution);

        return $institution;
    }

    protected static function makeTitle(ObjectManager $em) : Title
    {
        $random = self::randomNumber();

        $title = new Title();
        $title->setName("Test ${random}");
        $title->setSlug("test-${random}");

        $em->persist($title);
        $em->flush($title);

        return $title;
    }

    protected static function makeInstitutionTitle(ObjectManager $em) : InstitutionTitle
    {
        $institutionTitle = new InstitutionTitle();
        $institutionTitle->setTitle(self::makeTitle($em));
        $institutionTitle->setInstitution(self::makeInstitution($em));

        $em->persist($institutionTitle);
        $em->flush($institutionTitle);

        return $institutionTitle;
    }

    protected static function makeElection(ObjectManager $em) : Election
    {
        $random = self::randomNumber();

        $election = new Election();
        $election->setName("Test election ${random}");
        $election->setSlug("test-election-${random}");
        $election->setDate(new \DateTime());

        $em->persist($election);
        $em->flush($election);

        return $election;
    }

    protected static function makeCandidate(ObjectManager $em) : Candidate
    {
        $candidate = new Candidate();
        $candidate->setElection(self::makeElection($em));
        $candidate->setPolitician(self::makePolitician($em));
        $candidate->setConstituency(self::makeConstituency($em));

        $em->persist($candidate);
        $em->flush($candidate);

        return $candidate;
    }

    protected static function makeConstituency(ObjectManager $em) : Constituency
    {
        $random = self::randomNumber();

        $constituency = new Constituency();
        $constituency->setName("Test constituency ${random}");
        $constituency->setSlug("test-constituency-${random}");
        $constituency->setLink("http://constituency.test");
        $constituency->setNumber($random);

        $em->persist($constituency);
        $em->flush($constituency);

        return $constituency;
    }

    protected static function makeProblem(ObjectManager $em) : Problem
    {
        $random = self::randomNumber();

        $problem = new Problem();
        $problem->setName("Test problem ${random}");
        $problem->setSlug("test-problem-${random}");

        $em->persist($problem);
        $em->flush($problem);

        return $problem;
    }

    protected static function makePolitician(ObjectManager $em) : Politician
    {
        $random = self::randomNumber();

        $politician = new Politician();
        $politician->setFirstName("First ${random}");
        $politician->setLastName("Last ${random}");
        $politician->setSlug("foo-bar-${random}");

        $em->persist($politician);
        $em->flush($politician);

        return $politician;
    }

    protected static function makeParty(ObjectManager $em) : Party
    {
        $random = self::randomNumber();

        $party = new Party();
        $party->setName("Name ${random}");
        $party->setSlug("test-${random}");

        $em->persist($party);
        $em->flush($party);

        return $party;
    }

    protected static function makeMandate(ObjectManager $em) : Mandate
    {
        $mandate = new Mandate();
        $mandate->setBeginDate(new \DateTime("-2 years"));
        $mandate->setEndDate(new \DateTime("+2 years"));
        $mandate->setVotesCount(1000000);
        $mandate->setVotesPercent(73);
        $mandate->setInstitutionTitle(self::makeInstitutionTitle($em));
        $mandate->setPolitician(self::makePolitician($em));
        $mandate->setElection(self::makeElection($em));
        $mandate->setConstituency(self::makeConstituency($em));

        $em->persist($mandate);
        $em->flush($mandate);

        return $mandate;
    }

    protected static function makePromise(ObjectManager $em) : Promise
    {
        $random = self::randomNumber();

        $promise = new Promise();
        $promise->setName("Test ${random}");
        $promise->setSlug("test-${random}");
        $promise->setPublished(true);
        $promise->setPolitician(self::makePolitician($em));
        $promise->setElection(self::makeElection($em));
        $promise->setMadeTime(new \DateTime("-3 days"));

        $em->persist($promise);
        $em->flush($promise);

        return $promise;
    }

    protected static function makeAction(ObjectManager $em) : Action
    {
        $random = self::randomNumber();

        $action = new Action();
        $action->setName("Test ${random}");
        $action->setSlug("test-${random}");
        $action->setPublished(true);
        $action->setMandate(self::makeMandate($em));
        $action->setOccurredTime(new \DateTime());

        $em->persist($action);
        $em->flush($action);

        return $action;
    }

    protected static function makePromiseUpdate(
        ObjectManager $em, Action $action = null, Promise $promise = null
    ) : PromiseUpdate
    {
        if (!$promise) {
            $promise = self::makePromise($em);
            $promise->setPolitician($action->getMandate()->getPolitician());
            $promise->setElection($action->getMandate()->getElection());
        }

        $promiseUpdate = new PromiseUpdate();
        $promiseUpdate->setAction($action ? $action : self::makeAction($em));
        $promiseUpdate->setPromise($promise);

        $em->persist($promiseUpdate);
        $em->flush($promiseUpdate);
        $em->refresh($promiseUpdate->getAction());
        $em->refresh($promiseUpdate->getPromise());

        return $promiseUpdate;
    }

    protected static function makeActionSource(ObjectManager $em, Action $action = null) : ActionSource
    {
        $random = self::randomNumber();

        $actionSource = new ActionSource();
        $actionSource->setAction($action ? $action : self::makeAction($em));
        $actionSource->setName("Name ${random}");
        $actionSource->setLink("http://test.link/${random}");

        $em->persist($actionSource);
        $em->flush($actionSource);
        $em->refresh($actionSource->getAction());

        return $actionSource;
    }

    protected static function makePower(ObjectManager $em) : Power
    {
        $random = self::randomNumber();

        $party = new Power();
        $party->setName("Name ${random}");
        $party->setSlug("test-${random}");

        $em->persist($party);
        $em->flush($party);

        return $party;
    }

    protected static function makeConstituencyProblem(
        ObjectManager $em, Constituency $constituency = null) : ConstituencyProblem
    {
        $constituencyProblem = new ConstituencyProblem();
        $constituencyProblem->setConstituency($constituency ? $constituency : self::makeConstituency($em));
        $constituencyProblem->setElection(self::makeElection($em));
        $constituencyProblem->setProblem(self::makeProblem($em));

        $em->persist($constituencyProblem);
        $em->flush($constituencyProblem);
        $em->refresh($constituencyProblem->getConstituency());
        $em->refresh($constituencyProblem->getElection());
        $em->refresh($constituencyProblem->getProblem());

        return $constituencyProblem;
    }

    protected static function makeConstituencyCandidate(ObjectManager $em, Constituency $constituency = null) : Candidate
    {
        $candidate = new Candidate();
        $candidate->setConstituency($constituency ? $constituency : self::makeConstituency($em));
        $candidate->setElection(self::makeElection($em));
        $candidate->setPolitician(self::makePolitician($em));

        $em->persist($candidate);
        $em->flush($candidate);
        $em->refresh($candidate->getConstituency());
        $em->refresh($candidate->getElection());
        $em->refresh($candidate->getPolitician());

        return $candidate;
    }

    protected static function makeConstituencyCandidateProblemOpinion(
        ObjectManager $em, Constituency $constituency = null) : CandidateProblemOpinion
    {
        $opinion = new CandidateProblemOpinion();
        $opinion->setConstituency($constituency ? $constituency : self::makeConstituency($em));
        $opinion->setElection(self::makeElection($em));
        $opinion->setPolitician(self::makePolitician($em));
        $opinion->setProblem(self::makeProblem($em));
        $opinion->setOpinion("Test");

        $em->persist($opinion);
        $em->flush($opinion);
        $em->refresh($opinion->getConstituency());
        $em->refresh($opinion->getElection());
        $em->refresh($opinion->getPolitician());
        $em->refresh($opinion->getProblem());

        return $opinion;
    }

    protected static function makeCategory(ObjectManager $em) : Category
    {
        $random = self::randomNumber();

        $category = new Category();
        $category->setName("Test category ${random}");
        $category->setSlug("test-category-${random}");

        $em->persist($category);
        $em->flush($category);

        return $category;
    }

    protected static function makePromiseSource(ObjectManager $em, Promise $promise = null) : PromiseSource
    {
        $random = self::randomNumber();

        $promiseSource = new PromiseSource();
        $promiseSource->setPromise($promise ? $promise : self::makePromise($em));
        $promiseSource->setName("Name ${random}");
        $promiseSource->setLink("http://test.link/${random}");

        $em->persist($promiseSource);
        $em->flush($promiseSource);
        $em->refresh($promiseSource->getPromise());

        return $promiseSource;
    }

    protected static function makeBlogPost(ObjectManager $em) : BlogPost
    {
        $random = self::randomNumber();

        $blogPost = new BlogPost();
        $blogPost->setTitle("Test problem ${random}");
        $blogPost->setSlug("test-problem-${random}");
        $blogPost->setContent("Test ${random}". str_repeat(" Hello World", 10));
        $blogPost->setPublishTime(new \DateTime("-1 day"));

        $em->persist($blogPost);
        $em->flush($blogPost);

        return $blogPost;
    }
}
