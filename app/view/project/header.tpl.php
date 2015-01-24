<?php $user_id = $this->session->get('user_id'); $user = $this->session->get('user')?>

<span class='sitelogin'><?=isset($user) ? '<a href=' . $this->url->asset("users/id/$user_id") . ">$user</a> | <a href=" . $this->url->asset("users/logout") . '>Logga ut</a>' : '<a href=' . $this->url->asset("users/login") . '>Logga in</a> | <a href=' . $this->url->asset("users/register") . '>Registrera</a>'?></span>
<img class='sitelogo' src='<?=$this->url->asset("img/logo.svg")?>' alt='Site Logo'/>
<div class="headline">
<span class='sitetitle'><?=isset($siteTitle) ? $siteTitle : 'En titel'?></span>
<span class='siteslogan'><?=isset($siteTagline) ? $siteTagline : "En tagline"?></span>
</div>
