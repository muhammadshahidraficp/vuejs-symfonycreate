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
		$movie->setCount($request->get('count'));
		$em->persist($movie);
		$em->flush();

		return $this->respondCreated($movieRepository->transform($movie));
	}

	/**
	* @Route("/update", methods="POST")
	*/

	public function Update(Request $request,EntityManagerInterface $em,MovieRepository $movieRepository){
		$request_data= $request->request->all();
		$id=$request_data['id'];
		//print_r($request_data);
		$movie=$movieRepository->find($id);
		if(!$movie){
			return $this->respondNotFound();
		}
		$movie->setTitle($request->get('title'));
		$movie->setCount($request->get('count'));
		$em->persist($movie);
		$em->flush();

		return $this->respond([
			'count'=>$movie->getCount()
		]);
	}

/**
	* @Route("/delete", methods="POST")
	*/

	public function Delete(Request $request,EntityManagerInterface $em,MovieRepository $movieRepository){
		$request_data= $request->request->all();
		print_r($request_data);
		$id=$request_data['id'];
		$movie=$movieRepository->find($id);
		if(!$movie){
			return $this->respondNotFound();
		}
		

		$em->remove($movie);
  
       	$em->flush(); 


		return $this->respond([
			'count'=>$movie->getCount()
		]);
	}
}