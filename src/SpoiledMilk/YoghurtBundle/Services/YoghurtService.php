<?php

namespace SpoiledMilk\YoghurtBundle\Services;
use SpoiledMilk\YoghurtBundle\Entity as Entity;

class YoghurtService {

    private $container;
    private $uploadDir;

    public function __construct($container) {
        $this->container = $container;
        $this->uploadDir = __DIR__ . '/../../../../web/' . $this->container->getParameter('yoghurt_service.upload_dir');
    }

    /**
     *
     * @param $originalFile
     * @param string $newFileName
     */
    public function uploadFile($originalFile, $newFileName) {

        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        $ext = $originalFile->guessExtension();
        $newFileName = $newFileName . '.' . ($ext ? $ext : 'bin');
        $originalFile->move($this->uploadDir, $newFileName);
        return $newFileName;
    }

    public function deleteFile($fileName) {
        if (file_exists($this->uploadDir . '/' . $fileName)) {
            unlink($this->uploadDir . '/' . $fileName);
        }
    }

    /**
     *
     * @param string $fileName
     * @return string|null 'img' if the file is image, 'bin' if it's anything else,
     * and null if file doesn't exist or an error occured
     */
    public function getFileMimeType($fileName) {
        $path = $this->uploadDir . '/' . $fileName;
        $ret = null;

        try {
            $file = new \Symfony\Component\HttpFoundation\File\File($path);
            $type = strtolower($file->getMimeType());

            if (!$type) {
                $ret = null;
            } else if (stristr($type, 'image') != false) {
                $ret = 'img';
            } else {
                $ret = 'bin';
            }
        } catch (\Exception $e) {
            $ret = null;
        }

        return $ret;
    }

    /**
     * Removes files from upload directory that are not user by any entities
     * 
     * @return int Number of removed files
     */
    public function removeUnusedFiles() {
        if (!file_exists($this->uploadDir))
            return 0;
        
        $fileValues = $this->container->get('doctrine')
                ->getEntityManager()
                ->getRepository('SpoiledMilkYoghurtBundle:FileValue')
                ->findAll();
        
        $uploadedFiles = scandir($this->uploadDir);
        $usedFiles = array('.', '..');
        
        foreach ($fileValues as $fileVal) {
            $usedFiles[] = $fileVal->getValue();
        }
        
        $unusedFiles = array_diff($uploadedFiles, $usedFiles);

        foreach ($unusedFiles as $file) {
            unlink($this->uploadDir . '/' . $file);
        }
        
        return count($unusedFiles);
    }
    
    /**
     * Adds the set prefix to the name of the uploaded file
     *
     * @param \SpoiledMilk\YoghurtBundle\Entity\FileValue $fileValue
     */
    public function checkPrefix(Entity\FileValue $fileValue) {
        $fieldMeta = $fileValue->getField()->getFieldMeta();

        foreach ($fieldMeta as $fm) {
            if ($fm->getMetaKey() == 'prefix') {
                $fileValue->setPrefix($fm->getMetaValue());
                break;
            }
        }
    }

}