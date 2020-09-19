<?php


namespace App\Services;


use App\Repositories\CourseRepo;
use App\Repositories\UniversityRepo;

class UniversityService extends BaseService
{
    private $universityRepo;
    private $courseRepo;
    public function __construct(UniversityRepo $universityRepo, CourseRepo $courseRepo)
    {
        parent::__construct();
        $this->universityRepo = $universityRepo;
        $this->courseRepo = $courseRepo;
    }

    /**
     * get university list in database
     * @return mixed
     */
    public function universityList()
    {
        return $this->universityRepo->getAll();
    }

    /**
     * Get course belong to university
     * @param $universityId
     * @return mixed
     */
    public function getCourseByUniversity($universityId)
    {
        $query = $this->courseRepo->model->select('id', 'name', 'slug');

        if ($universityId) {
            $courses = $this->cacheHelper->getCache("course_$universityId");
            if ($courses) {
                return $courses;
            }

            $courses = $query->where('university_id', $universityId)
                                ->orderBy('name', 'ASC')->get();

            $this->cacheHelper->setCache("course_$universityId", $courses);
        } else {
            $courses = $this->cacheHelper->getCache("all_course");
            if ($courses) {
                return $courses;
            }

            $courses = $query->orderBy('name', 'ASC')->get();
            $this->cacheHelper->setCache("all_course", $courses);
        }

        return $courses;
    }
}
