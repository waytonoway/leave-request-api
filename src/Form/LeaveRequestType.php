<?php
namespace App\Form;

use App\Entity\LeaveRequest;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints as Assert;

class LeaveRequestType extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add("start_date", TextType::class)
            ->add("end_date", TextType::class)
            ->add("leave_type", EntityType::class, [
                "class" => "App\Entity\LeaveType"
            ])
            ->add("reason", TextareaType::class, [
                "constraints" => [
                    new Assert\NotBlank(),
                    new Assert\Length(["max" => 50]),
                ],
            ])
            ->add("user", EntityType::class, [
                "class" => "App\Entity\User"
            ])
            ->add("save", SubmitType::class, ["label" => "Save Leave Request"])
            ->addEventListener(FormEvents::PRE_SUBMIT, function(FormEvent $event) {
                $data = (array)$event->getData();

                $data["start_date"] = new \DateTime($data["start_date"]);
                $data["end_date"] = new \DateTime($data["end_date"]);

                $event->setData($data);
            });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            "data_class" => LeaveRequest::class,
        ]);
    }
}

