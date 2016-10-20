<?php

abstract class SocialSharing_Projects_Builder_Standard extends SocialSharing_Projects_Builder
{

     /**
     * Returns default button classes.
     * @return array
     */
    protected function getButtonClasses()
    {
        $name = strtolower($this->getName());
        $project = $this->getProject();
        $classes = array(
            'social-sharing-button',
            'sharer-' . $name,
            'sharer-' . $project->get('design', $name . '-1'),
            'counter-' . strtolower(
                $project->get(
                    'shares_style',
                    SocialSharing_Projects_Builder::COUNTER_STANDARD
                )
            )
        );

        // Gradient mode
        if ($project->has('grad')) {
            $classes[] = 'grad';
        }

        // Hide counters
        if (!$project->isDisplayTotalShares()) {
            $classes[] = 'without-counter';
        }

        return $classes;
    }

    /**
     * Returns button composite.
     * @param SocialSharing_Projects_Builder_Network $network
     * @return SocialSharing_HtmlBuilder_AbstractElement
     */
    public function getButton(SocialSharing_Projects_Builder_Network $network)
    {
        $builder = $this->getBuilder();
        $project = $this->getProject();
        $classes = $this->getButtonClasses();
        $current = $this->getCurrentPost($project->isSharePostLinkInList());

        if ((int)$project->get('show_counter_after', 0) > $network->getShares()) {
            $classes[] = 'without-counter';
        }

        if (false != $network->getTooltip()) {
            $classes[] = 'tooltip-icon';
        }

        if ($network->getIconImageID()) {
            $classes[] = 'button-icon-image';
        }

        $classes[] = $network->getClass();

        $currentID = $current ? $current->ID : get_the_ID();

        if($current) {
            if ($description = $current->post_excerpt) {
                $description = strip_tags($current->post_excerpt);
                $description = str_replace("", "'", $description);
            } else {
                $description = $current->post_title;
            }
        } else {
            $description = get_bloginfo('description');
        }

        $atributesButton = array(
            $builder->createAttribute(
                'class',
                $classes
            ),
            $builder->createAttribute(
                'target',
                '_blank'
            ),
            $builder->createAttribute(
                'title',
                $network->getTitle()
            ),
            $builder->createAttribute(
                'href',
                $network->getUrl($current,$project)
            ),
            $builder->createAttribute(
                'data-nid',
                $network->getId()
            ),
            $builder->createAttribute(
                'data-pid',
                $project->getId()
            ),
            $builder->createAttribute(
                'data-post-id',
                $currentID
            ),
            $builder->createAttribute(
                'data-url',
                admin_url('admin-ajax.php')
            ),
            $builder->createAttribute(
                'data-url',
                admin_url('admin-ajax.php')
            ),
            $builder->createAttribute(
                'data-description',
                $description
            ),
            $builder->createAttribute(
                'rel',
                'nofollow'
            )
        );

        $atributesIcon = array();

        if ($network->getIconImageID()) {
            $atributesButton[] = $builder->createAttribute(
                'style',
                'background-image: url(' . wp_get_attachment_image_url($network->getIconImageID()) . ');' .
                'background-size: 100% 100%; background-position: center;'
            );
        }

        $atributesIcon[] = $builder->createAttribute(
            'class',
            array('fa', 'fa-fw', $network->getIcon())
        );

        $button = $builder->createElement('a', $atributesButton);

        $icon = $builder->createElement('i', $atributesIcon);

        $counter = $builder->createElement(
            'div',
            array(
                $builder->createAttribute(
                    'class',
                    array(
                        'counter-wrap',
                        $project->get(
                            'shares_style',
                            SocialSharing_Projects_Builder::COUNTER_STANDARD
                        )
                    )
                )
            )
        );

        $counter->addElement(
            $builder->createElement(
                'span',
                array(
                    $builder->createAttribute('class', 'counter')
                )
            )->addElement(
                $builder->createTextElement(
                    $network->getShares(
                        $project->isShortNumbers()
                    )
                )
            )
        );

        $button->addElement($icon);
        $button->addElement($counter);

        return $button;
    }
}
