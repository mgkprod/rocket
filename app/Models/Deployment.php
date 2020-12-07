<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Deployment extends Model
{
    use Concerns\HasUlid;

    protected $guarded = [];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function getRelativePath($basePath, $relativePath = '')
    {
        return rtrim($basePath, '/') . ($relativePath ? '/' . ltrim($relativePath, '/') : '');
    }

    public function getDeployPath($path = '')
    {
        return $this->getRelativePath($this->project->deploy_path, $path);
    }

    public function getReleasePath($release, $path = '')
    {
        return $this->getRelativePath($this->getRelativePath($this->getDeployPath('releases'), $release), $path);
    }

    public function buildFingerprint()
    {
        return sha1($this->server->ssh_host . $this->project->deploy_path);
    }

    public function buildServerString()
    {
        $host = $this->server->ssh_user . '@' . $this->server->ssh_host;
        $ssh_pem_file = storage_path('app/id_rsa');
        $options = '-q -A -i ' . escapeshellarg($ssh_pem_file);

        if ($this->server->ssh_options) {
            $options .= ' ' . $this->server->ssh_options;
        }

        return $options . ' ' . $host;
    }

    public function extractVariables()
    {
        return [
            'release' => $this->release,
            'commit' => $this->commit,
            'ssh_host' => $this->server->ssh_host,
            'ssh_user' => $this->server->ssh_user,
            'deploy_path' => $this->project->deploy_path,

            'ssh_options' => $this->server->ssh_options,
            'repository_url' => $this->project->repository_url,
            'linked_files' => $this->project->linked_files ?? [],
            'linked_dirs' => $this->project->linked_dirs ?? [],
            'copied_files' => $this->project->copied_files ?? [],
            'copied_dirs' => $this->project->copied_dirs ?? [],
            'cron_jobs' => $this->project->cron_jobs ?? null,
            'keep_releases' => $this->project->keep_releases ?? 5,
            'cmd_git' => $this->server->cmd_git ?? 'git',
            'cmd_npm' => $this->server->cmd_npm ?? 'npm',
            'cmd_yarn' => $this->server->cmd_npm ?? 'yarn',
            'cmd_bower' => $this->server->cmd_bower ?? 'bower',
            'cmd_grunt' => $this->server->cmd_grunt ?? 'grunt',
            'cmd_php' => $this->server->cmd_php ?? 'php',
            'cmd_composer' => $this->server->cmd_composer ?? 'composer',
            'cmd_composer_options' => $this->server->cmd_composer_options ?? '--no-dev',

            // Variables computed internally
            'fingerprint' => $this->buildFingerprint(),
            'server_string' => $this->buildServerString(),

            // Variables computed internally that defined paths
            'repo_path' => $this->getDeployPath('repo'),
            'repository_path' => $this->getDeployPath('repository'),
            'current_path' => $this->getDeployPath('current'),
            'releases_path' => $this->getDeployPath('releases'),
            'release_path' => $this->getReleasePath($this->release),
            'assets_path' => $this->getReleasePath($this->release),
            'shared_path' => $this->getDeployPath('shared'),
            'backups_path' => $this->getDeployPath('backups'),
        ];
    }
}