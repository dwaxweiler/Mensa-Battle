<?php
namespace MensaBattle\FacebookAppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BattleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title');
        $builder->add('description');
        $builder->add('startTime');
        $builder->add('endTime');
        $builder->add('participationScore');
        $builder->add('trophy', new TrophyType());
    }
    
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MensaBattle\APIBundle\Entity\Battle',
            'cascade_validation' => true
        ));
    }
    
    public function getName()
    {
        return 'battle';
    }
}