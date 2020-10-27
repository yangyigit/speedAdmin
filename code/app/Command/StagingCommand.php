<?php

declare(strict_types=1);

namespace App\Command;

use App\Tools\Staging;
use Hyperf\Command\Command as HyperfCommand;
use Hyperf\Command\Annotation\Command;
use Hyperf\DbConnection\Db;
use Hyperf\Di\Annotation\Inject;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * @Command
 */
class StagingCommand extends HyperfCommand
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject()
     * @var Staging
     */
    protected $staging;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct('custom:staging');
    }

    public function configure()
    {
        parent::configure();
        $this->setDescription('Create basic structure');
        $this->addOption('class','C',InputOption::VALUE_REQUIRED,'类名');
        $this->addOption('table','T',InputOption::VALUE_REQUIRED,'表名');
        $this->addOption('search','S',InputOption::VALUE_IS_ARRAY | InputOption::VALUE_OPTIONAL,'需要搜索的字段，用英文逗号隔开');
        $this->addOption('explain_class','E',InputOption::VALUE_REQUIRED,'类注释');
        $this->addOption('author','A',InputOption::VALUE_REQUIRED,'作者');

    }

    public function handle()
    {
        $count = 0;
        if ($this->input->hasOption('class')) {
            $class = $this->input->getOption('class');
        } else {
            $this->output->writeln('error, class defect.');
            return false;
        }

        if ($this->input->hasOption('table')) {
            $table = $this->input->getOption('table');
        } else {
            $this->output->writeln('error, table defect.');
            return false;
        }

        if ($this->input->hasOption('search')) {
            $search = $this->input->getOption('search');
        } else {
            $search = '';
        }

        if ($this->input->hasOption('explain_class')) {
            $explain_class = $this->input->getOption('explain_class');
        } else {
            $this->output->writeln('error, explain_class defect.');
            return false;
        }

        if ($this->input->hasOption('author')) {
            $author = $this->input->getOption('author');
        } else {
            $this->output->writeln('error, author defect.');
            return false;
        }

        //解析表字段
        $res_db = getTableFields($table);

//        //创建控制器代码
//        if($this->staging->createController($class, $table, $explain_class, $author, $res_db)){
//            $count++;
//        }

        //创建网页列表代码
        if($this->staging->createListView($class, $search, $res_db, $explain_class, $table)){
            $count++;
        }
//
//        //创建网页新增代码
//        if($this->staging->createAddView($class, $res_db, $explain_class)){
//            $count++;
//        }
//
//        //创建网页编辑代码
//        if($this->staging->createEditView($class, $res_db, $explain_class)){
//            $count++;
//        }

//        $this->output->progressStart($count);
//        for($i = 1; $i<=$count; $i++){
//            $this->output->progressAdvance(1);
//        }
//        $this->output->progressFinish();
//        $this->output->writeln('create success');
    }


}
