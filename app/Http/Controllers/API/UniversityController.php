<?php


namespace App\Http\Controllers\API;


use App\Http\Controllers\Controller;
use App\Services\UniversityService;
use Illuminate\Http\Request;

class UniversityController extends Controller
{
    private $universityService;
    public function __construct(UniversityService $universityService)
    {
        $this->universityService = $universityService;
    }

    public function getUniversity()
    {
        $universities = $this->universityService->universityList();
        success($universities);
    }

    public function getCourse(Request $request)
    {
        $data = getData($request);
        if (!isset($data['university_id'])) {
            $data['university_id'] = null;
        }

        $courses = $this->universityService->getCourseByUniversity($data['university_id']);
        success($courses);
    }
}
