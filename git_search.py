import os
import re
from git import Repo
from git import Git
import multiprocessing as mp
import tempfile
import shutil


def clone_repo(repo_url, local_path):
    try:
        Repo.clone_from(repo_url, local_path)
        print(f"Success cloning {repo_url}")
    except Exception as e:
        print(f"Failed loning {repo_url}:")
        print(str(e))

def read_file(file_path):
    encodings = ['utf-8', 'iso-8859-1', 'windows-1252']
    for encoding in encodings:
        try:
            with open(file_path, 'r', encoding=encoding) as f:
                return f.read()
        except UnicodeDecodeError:
            continue
    return None  

def search_passwords(repo_path, repo_url, passwords):
    found_strings = []
    
    # Search in current files
    for root, _, files in os.walk(repo_path):
        for file in files:
            file_path = os.path.join(root, file)
            content = read_file(file_path)
            if content is not None:
                for password in passwords:
                    if re.search(password, content, re.IGNORECASE):
                        relative_path = os.path.relpath(file_path, repo_path)
                        found_strings.append((password, f"{repo_url}/blob/master/{relative_path}"))

    # Search in git history
    repo = Repo(repo_path)
    g = Git(repo_path)
    for commit in repo.iter_commits(all=True):
        for password in passwords:
            if re.search(password, commit.message, re.IGNORECASE):
                found_strings.append((password, f"{repo_url}/commit/{commit.hexsha}"))
        
    # Search in file changes
        try:
            diff = g.show('--pretty=format:', '--name-only', commit.hexsha)
            changed_files = diff.split('\n')
            for file in changed_files:
                if file:
                    try:
                        file_content = g.show(f'{commit.hexsha}:{file}')
                        for password in passwords:
                            if re.search(password, file_content, re.IGNORECASE):
                                found_strings.append((password, f"{repo_url}/blob/{commit.hexsha}/{file}"))
                    except:
                        # File might not exist in this commit
                        pass  
        except:
            pass  
    return found_strings


def process_repo(repo_url, passwords, temp_dir):
    repo_name = repo_url.split('/')[-1].replace('.git', '')
    local_path = f'{temp_dir}/{repo_name}'

    clone_repo(repo_url, local_path)
    found_passwords = search_passwords(local_path, repo_url, passwords)

    return repo_name, found_passwords


def cleanup_repos(dir_path):
    print("Removing cloned repositories...")
    shutil.rmtree(dir_path, ignore_errors=True)
    print(f"Removed {dir_path}")

def main():
    temp_dir = tempfile.mkdtemp()
    
    print(f"Temp dir created at {temp_dir}")

    with open('repos.txt', 'r') as f:
        repos = f.read().splitlines()

    with open('passwords.txt', 'r') as f:
        passwords = f.read().splitlines()

    # Create a pool of workers cpu_count - 2
    pool = mp.Pool(processes=mp.cpu_count()-2)

    results = pool.starmap(process_repo, [(repo, passwords, temp_dir) for repo in repos])

    pool.close()
    pool.join()

    # Process, print the results, and write to file
    with open('git_found_passwords.txt', 'w') as output_file:
        for repo_name, found_passwords in results:
            if found_passwords:
                print(f"Passwords found in {repo_name}:")
                output_file.write(f"Passwords found in {repo_name}:\n")
                for password, location in found_passwords:
                    print(f"- {password} at {location}")
                    output_file.write(f"- {password} at {location}\n")
            else:
                print(f"No passwords found in {repo_name}")
                output_file.write(f"No passwords found in {repo_name}\n")
            print("---")
            output_file.write("---\n")

    print(f"Results have been written to git_found_passwords.txt")

    # Delete temp dir
    cleanup_repos(temp_dir)

if __name__ == "__main__":
    main()