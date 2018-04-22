<?php assert($content instanceof \Lcobucci\ContentNegotiation\Tests\PersonDto) ?>
<section>
    <dl>
        <dt>Identifier</dt>
        <dd><?=$this->e($content->id)?></dd>
        <dt>Name</dt>
        <dd><?=$this->e($content->name)?></dd>
    </dl>
</section>
