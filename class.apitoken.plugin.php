<?php

class ApitokenPlugin extends Gdn_Plugin {
    public function setup() {
        touchConfig(
            [
                'apitoken.expires' => '1 month',
                'apitoken.name' => 'apitoken',
                'apitoken.scope' => ''
            ]
        );
    }

    public function pluginController_apiToken_create($sender, $args) {
        // Enforce POST requests for added security.
        if (Gdn::request()->isPostBack() === false) {
            throw new Exception('Requires POST', 405);
        }

        // Test authentication.
        $authenticator = Gdn::authenticator()->authenticateWith('password');
        $userID = $authenticator->authenticate(
            Gdn::request()->post('email', ''),
            Gdn::request()->post('password', '')
        );

        if ($userID == 0) {
            throw new Exception('Authorization required', 401);
        }

        // Check if there is already such a token for this user.
        $accessTokenModel = new AccessTokenModel();
        $accessToken = $accessTokenModel->getWhere(
            [
                'UserID' => $userID,
                'Type' => c('apitoken.name'),
                'DateExpires >=' => Gdn_Format::toDateTime()

            ]
        )->firstRow(DATASET_TYPE_ARRAY);
        if ($accessToken == []) {
            // Issue new token if needed.
            $token = $accessTokenModel->issue(
                $userID,
                c('apitoken.expires'),
                c('apitoken.name'),
                explode(',', c('Apitoken.scope'))
            );
        } else {
            // If oken exists, sign with expiry date.
            $token = $accessTokenModel->signToken(
                $accessToken['Token'],
                $accessToken['DateExpires']
            );
        }

        // Return the signed token
        echo json_encode(['access_token' => $token]);
    }

    public function vanillaController_apiTokenTest_create($sender) {
        $sender->Form = new Gdn_Form();
        $sender->render('apitokentest', '', 'plugins/apitoken');
    }
}
