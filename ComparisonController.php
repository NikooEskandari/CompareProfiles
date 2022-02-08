 <?php

namespace Map2u\Manifold\MapsBundle\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Default controller.
 *
 * @Route("/comparson")
 */
class ComparisonController extends Controller {

 
 
 /**
     *  Just for practice React
     *
     * @Route("/react_version", name="comparison_react_version", options={"expose"=true})
     * @Method("GET")
     * @Template()
     */
    public function react_versionAction(Request $request) {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute("fos_user_security_login");
        }

        $last_path = $request->get('homeleftbar_route');
        $report_data_year = $this->getParameter('year_of_report_data');

        $request->getSession()->set("leftsidebar_route", 'comparison_react_version');
        $request->getSession()->set("homepage_route", 'comparison_react_version');
        $request->getSession()->set("homeleftbar_route", $last_path);
        $tradeareas_from_mapping = $request->getSession()->get("selected_tradeareas");

        $conn = $this->get("database_connection");
        $selectedprofiles = $request->getSession()->get("comparison_selectedprofiles");
        $em = $this->getDoctrine()->getManager();
        $customerfiles = array();
        $markets = array();
        $tradeareafiles = array();
        $tradeareas = array();
        $favorites = array();
        $default_benchmark = null;
        $default_benchmark_entity = $em->getRepository('Map2uCoreBundle:SystemSettings')->findOneBy(array('userId' => $user->getId(), 'name' => 'Default Benchmark'));
        if ($default_benchmark_entity) {
            $default_benchmark = $default_benchmark_entity->getSettings();
        }

        if ($user) {
            $allvision_user_id=AllvisionReportMethods::getAllvisionAtPolarisUserId($this,$request);

            if($user->hasGroup('Allvision') && $allvision_user_id) {
                $customerfiles = $em
                    ->getRepository('Map2uManifoldMapsBundle:SpatialFile')
                    ->createQueryBuilder('s')
                    ->select("s.supportType, s.userId, s.name, s.id, s.valueField, s.recordCount, s.pcCount, s.updatedAt, s.selectedFields")
                    ->where("((s.user =:user ) or ( s.userId ='".$allvision_user_id."')) and (s.supportType in ('3', '15'))")
                    ->andWhere("s.selectedFields is not null and s.fieldList is not null and s.recordCount is not null")
                    ->setParameter("user", $user)
                    ->getQuery()
                    ->getResult();
            }
            else {
                $customerfiles = $em
                    ->getRepository('Map2uManifoldMapsBundle:SpatialFile')
                    ->createQueryBuilder('s')
                    ->select("s.supportType, s.userId, s.name, s.id, s.valueField, s.recordCount, s.pcCount, s.updatedAt, s.selectedFields")
                    ->where("(s.user =:user ) and (s.supportType in ('3', '15'))")
                    ->andWhere("s.selectedFields is not null and s.fieldList is not null and s.recordCount is not null")
                    ->setParameter("user", $user)
                    ->getQuery()
                    ->getResult();
            }

            $tradeareafiles = $conn->fetchAll("select name, id, support_type, selected_fields::json ->> 'spatial_loc_pc' as pc_column, "
                . " selected_fields::json ->> 'spatial_geographic_id' as geoId_column, "
                . " selected_fields::json ->> 'spatial_loc_category' as category_column, updated_at"
                . " from manifold_spatialfiles "
                . " where user_uuid = '" . str_replace('_', '-', $user->getId()) . "' "
                . " and (support_type in ('2', '8', '9', '10', '11', '12', '13', '16') )"
                . " order by name asc");

            $tradeareas = $em->getRepository("Map2uManifoldMapsBundle:TradeArea")
                ->createQueryBuilder('c')
                ->select("c.name, c.id, c.updatedAt, c.drawtype, c.locType, c.tradeareaType, c.radius")
                ->andWhere('(c.user =:user) and (c.deleted = 0)')
                ->setParameter("user", $user)
                ->orderBy("c.updatedAt", 'DESC')
                ->orderBy("c.name", 'ASC')
                ->getQuery()
                ->getResult();

            $sql = "select * from manifold_myfavorites where user_uuid = '" . $user->getId() . "'";
            $favorites = $conn->fetchAll($sql);

            $markets = ManifoldDefaultMethods::buildPreDefinedMarketsStructureData($conn, $report_data_year);
        }

        return array('homepage_route' => $request->getSession()->get("homepage_route"), 'customerfiles' => $customerfiles
        , 'selectedprofiles' => $selectedprofiles, 'tradeareafiles' => $tradeareafiles, 'tradeareas' => $tradeareas
        , 'favorites' => $favorites, 'default_benchmark' => $default_benchmark, 'markets' => $markets
        , "tradeareas_from_mapping" => $tradeareas_from_mapping);
    }
	
	
}