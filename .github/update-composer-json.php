<?php

// Load the composer.json and composer.lock files
$composerJsonPath = 'composer.json';
$composerLockPath = 'composer.lock';

// Check if the files exist
if (!file_exists($composerJsonPath) || !file_exists($composerLockPath)) {
    echo "composer.json or composer.lock file is missing!";
    exit(1);
}

// Read and decode the composer.json file
$composerJson = json_decode(file_get_contents($composerJsonPath), true);
if ($composerJson === null) {
    echo "Error reading composer.json!";
    exit(1);
}

// Read and decode the composer.lock file
$composerLock = json_decode(file_get_contents($composerLockPath), true);
if ($composerLock === null) {
    echo "Error reading composer.lock!";
    exit(1);
}

// Check if "repositories" exists in composer.json; if not, create it
if (!isset($composerJson['repositories'])) {
    $composerJson['repositories'] = [];
}

// Prepare to track any newly pinned packages
$pinnedPackages = [];

// Function to convert exact version to a range (caret operator with full version)
function convertToVersionRange($version) {
    // If version looks like an exact version (e.g., "2.0.53"), convert it to a range with caret
    if (preg_match('/^(\d+)\.(\d+)\.(\d+)$/', $version, $matches)) {
        return "^" . $matches[1] . "." . $matches[2] . "." . $matches[3];  // Convert to ^X.Y.Z (e.g., ^2.0.53)
    }
    // If already a version range (like "^2.0" or "~2.0"), just return it
    return $version;
}

// Iterate through the packages in composer.lock
foreach ($composerLock['packages'] as $package) {
    $packageName = $package['name'];
    $packageVersion = $package['version'];

    // Convert exact version to a range using the convertToVersionRange function
    $packageVersionRange = convertToVersionRange($packageVersion);

    // Update the versions in the "require" section if the package exists
    if (isset($composerJson['require'][$packageName])) {
        $composerJson['require'][$packageName] = $packageVersionRange;
    }

    // If the package exists in "require-dev", update that as well
    if (isset($composerJson['require-dev'][$packageName])) {
        $composerJson['require-dev'][$packageName] = $packageVersionRange;
    }

    // Get the repository URL and dist type from composer.lock (either from 'dist' or 'source')
    $repositoryUrl = null;
    $packageHash = null;
    $distType = 'tar'; // Default to 'tar' if no type is specified

    if (isset($package['dist']['url'])) {
        $repositoryUrl = $package['dist']['url'];
        $packageHash = isset($package['dist']['reference']) ? $package['dist']['reference'] : null;
        // Set the dist type based on the 'dist' section in composer.lock
        if (isset($package['dist']['type'])) {
            $distType = $package['dist']['type']; // Use the dist type from composer.lock
        }
    } elseif (isset($package['source']['url'])) {
        $repositoryUrl = $package['source']['url'];
        $packageHash = isset($package['source']['reference']) ? $package['source']['reference'] : null;
        // If the package comes from a VCS, we can set type as 'vcs'
        $distType = 'vcs';
    }

    // If no URL found, skip this package (you can handle this case as needed)
    if (!$repositoryUrl) {
        echo "No repository URL found for package: $packageName\n";
        continue;
    }

    // Pin the package in the "repositories" section if it's not already pinned
    $repositoryExists = false;

    foreach ($composerJson['repositories'] as &$repo) {
        if (isset($repo['package']) && $repo['package']['name'] === $packageName) {
            // Update existing repository entry with the new version, URL, hash, and dist type
            $repo['package']['version'] = $packageVersionRange;
            $repo['package']['dist']['url'] = $repositoryUrl;
            if ($packageHash) {
                $repo['package']['dist']['reference'] = $packageHash;
            }
            $repo['package']['dist']['type'] = $distType;
            $repositoryExists = true;
            break;
        }
    }

    // If repository entry does not exist, add a new one
    if (!$repositoryExists) {
        $composerJson['repositories'][] = [
            'type' => 'package',  // The type is at the root level of the repository entry
            'package' => [
                'name' => $packageName,
                'version' => $packageVersionRange,
                'type' => 'library',  // Default type, you can adjust this if needed
                'dist' => [
                    'url' => $repositoryUrl,
                    'type' => $distType,  // Use the dynamically derived dist type
                    'reference' => $packageHash // Adding the hash (reference) if available
                ]
            ]
        ];
        // Track pinned package
        $pinnedPackages[] = $packageName;
    }
}

// Write the updated composer.json back to the file
if (file_put_contents($composerJsonPath, json_encode($composerJson, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL)) {
    echo "composer.json has been updated with the versions from composer.lock.\n";
    if (count($pinnedPackages) > 0) {
        echo "The following packages were pinned to repositories:\n";
        foreach ($pinnedPackages as $packageName) {
            echo "  - $packageName\n";
        }
    }
} else {
    echo "Failed to update composer.json.\n";
    exit(1);
}
