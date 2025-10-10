## Version : About
In order to protect the semantic versioning mentality, we thought that it would be more accurate to proceed with a
certain mentality in version upgrades. For this reason, we created a release algorithm. Detailed information about
this release algorithm is given below.

- `MAIN` - `UPDATE` - `PATCH`
    - When we make a major change that is incompatible with the previous version, a version jump occurs in the **"MAIN"** part. (**2**.0.0)
    - When we make an update / change compatible with the previous version, a version jump occurs in the **"UPDATE"** section. (2.**1**.0)
    - When we make / apply a bug fix compatible with the previous version, a version jump occurs in the **"PATCH"** section. (2.1.**1**)

- `DEV` - `PRE` - `STABLE`
    - It is added to the end of the versions that are still under development and definitely not ready for use. (2.1.1-**dev**)
    - Tests in the development phase are completed and added to the end of the version presented to the user. (2.1.1-**pre**)
    - It is added to the end of the stable version, which is found to be suitable for use as a result of users and tests. (2.1.1-**stable**)

The project will be structured and developed to target from the nostalgia version to the most recent version in use. Do
not compare the file versions with the expansion pack versions of the game. A different commit branch will be created
for each release. These commit branches will be added as the release progresses.

## Version : Schema

| Product Version | Product Last Support Date         | Product Release Date              | Target   | LTS                | Status             |
|-----------------|-----------------------------------|-----------------------------------|:--------:|:------------------:|:------------------:|

> **Note**
> Version release dates may be earlier, it's just planned this way so that it can be followed by the entire community.

## Version : Information
- The support life of the versions marked as LTS is determined as 3 years.
- The support life of the versions marked as Non-LTS is determined as 1 year.
- The target column represents the expansion packs of the official client.
- It means that the versions marked in the status column are actively published.
- **Selçuk Çukur** always reserves the right to change the release date of the version.
