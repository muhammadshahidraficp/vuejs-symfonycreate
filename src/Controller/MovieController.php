<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Movie;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\MovieRepository;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkworkBundle\Configuration\Method;



class MovieController extends ApiController{


	/**
	* @Route("/movies", methods="GET")
	*/
	public function index(MovieRepository $movieRepository){
		
		$movies=$movieRepository->transformAll();
		return $this->respond($movies);
	}

	/**
	* @Route("/movies", methods="POST")
	*/
	public function create(Request $request, MovieRepository $movieRepository, EntityManagerInterface $em){
		
		echo "x";
		

		//$request=$this->transformJsonBody($request);
		$request_data = $request->request->all();
		print_r($request_data);
		if(! $request){
			return $this->respondValidationError('Please provide a request !');
		}

		//validate the title
		if(! $request->get('title')){
			return $this->respondValidationError('Please provide a title !');
		}

		//persist the new movie record
		$movie = new Movie;
		$movie->setTitle($request->get('title'));
		$movie->setCount(0);
		$em->persist($movie);
		$em->flush();

		return $this->respondCreated($movieRepository->transform($movie));
	}

	/**
	* @Route("/movies/{id}/count", methods="POST")
	*/

	public function increaseCount($id,EntityManagerInterface $em,MovieRepository $movieRepository){
		$movie=$MovieRepository->find($id);
		if(!$movie){
			return $this->respondNotFound();
		}

		$movie->setCount($movie->getCount()+1);
		$em->persist($movie);
		$em->flush();

		return $this->respond([
			'count'=>$movie->getCount()
		]);
	}

}