<?php

namespace LimeSurvey\Models\Services;

use LSHttpRequest;
use LSWebUser;
use SurveysGroups;
use SurveysGroupsettings;

/**
 * Service class for survey group creation.
 * All dependencies are injected to enable mocking.
 */
class SurveyGroupCreator
{
    /** @var LSHttpRequest */
    private $request;

    /** @var LSWebUser */
    private $user;

    /** @var SurveysGroups */
    private $surveysGroup;

    /** @var $surveysGroupsettings */
    private $surveysGroupsettings;

    /**
     * @param LSHttpRequest $request
     * @param LSWebUser $user
     * @param SurveysGroups $surveysGroup
     * @param SurveysGroupsettings $surveysGroupsettings
     */
    public function __construct(
        LSHttpRequest $request,
        LSWebUser $user,
        SurveysGroups $surveysGroup,
        SurveysGroupsettings $surveysGroupsettings
    ) {
        $this->request = $request;
        $this->user = $user;
        $this->surveysGroup = $surveysGroup;
        $this->surveysGroupsettings = $surveysGroupsettings;
    }

    /**
     * Save the SurveysGroups and SurveysGroupsettings models with data from the request.
     *
     * @return boolean
     * @todo What happen if SurveysGroups saved but no SurveysGroupsettings?
     */
    public function save()
    {
        $this->surveysGroup->attributes = $this->request->getPost('SurveysGroups');
        $this->surveysGroup->created_by = $this->user->id;
        if ($this->surveysGroup->save()) {
            // Save new SurveysGroupsettings record
            $this->surveysGroupsettings = new SurveysGroupsettings();
            $this->surveysGroupsettings->gsid = $this->surveysGroup->gsid;
            $this->surveysGroupsettings->setToInherit();

            return $this->surveysGroupsettings->save();
        } else {
            return false;
        }
    }
}
