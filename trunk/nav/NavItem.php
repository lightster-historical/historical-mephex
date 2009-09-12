<?php


class NavItem
{
    private $id;

    private $title;
    private $keyName;
    private $link;
    private $parent;
    private $useQS;

    private $permissionDomain;
    private $permission;

    private $children;



    public function NavItem($id, $keyName, $title, NavItem $parent = null
        , $link = null, $permissionDomain = null, $permission = null, $useQS = false)
    {
        $this->id = $id;

        $this->keyName = $keyName;
        $this->title = $title;
        $this->parent = $parent;
        $this->link = $link;
        $this->useQS = ($useQS == '1');

        $this->permissionDomain = $permissionDomain;
        $this->permission = $permission;

        if(!is_null($parent))
            $parent->addChild($this);

        $this->children = array();
    }


    public function getId()
    {
        return $this->id;
    }


    public function getKeyName()
    {
        return $this->keyName;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function getLink()
    {
        return $this->link;
    }

    public function useQueryString()
    {
        return $this->useQS;
    }

    public function getPermissionDomain()
    {
        return $this->permissionDomain;
    }

    public function getPermission()
    {
        return $this->permission;
    }

    public function getURL()
    {
        $url = '';

        if(!is_null($this->getLink()))
            $url = $this->getLink();
        else
        {
            $parent = $this;
            do
            {
                $url = '/' . $parent->getKeyName() . $url;
                $parent = $parent->getParent();
            }
            while(!is_null($parent));
        }

        if($this->useQS && array_key_exists('QUERY_STRING', $_SERVER)
            && trim($_SERVER['QUERY_STRING']) != '')
        {
            if(strstr($url, '?') === false)
                $url .= '?';
            else
                $url .= '&amp;';

            $url .= str_replace('&', '&amp;', $_SERVER['QUERY_STRING']);
        }

        return $url;
    }

    public function getChildren()
    {
        return $this->children;
    }



    public function setParent(NavItem $parent = null)
    {
        if(!($this->parent === $parent))
        {
            if(!is_null($this->parent))
            {
                $this->parent->removeChild($this);
                $this->parent = null;
            }

            if(!is_null($parent))
            {
                $this->parent = $parent;
                $parent->addChild($this);
            }
        }
    }



    public function addChild(NavItem $child)
    {
        if($child->getParent() === $this)
        {
            $this->children[] = $child;
        }
        else
        {
            echo 'Invalid child NavItem';
        }
    }

    public function removeChild(NavItem $child)
    {
        $key = array_search($child, $this->children, true);

        if(!($key === FALSE))
        {
            unset($this->children[$key]);
        }
    }
}


?>
