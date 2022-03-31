package logic

import (
	"backend/common/errorx"
	"backend/service/k8s/cmd/api/internal/svc"
	"backend/service/k8s/cmd/api/internal/types"
	"context"
	"github.com/zeromicro/go-zero/core/logx"
	"log"
	"strconv"
	"strings"

	appsV1 "k8s.io/api/apps/v1"
	coreV1 "k8s.io/api/core/v1"
	metav1 "k8s.io/apimachinery/pkg/apis/meta/v1"
	"k8s.io/client-go/kubernetes"
	"k8s.io/client-go/tools/clientcmd"
)

type CreateDeploymentLogic struct {
	logx.Logger
	ctx    context.Context
	svcCtx *svc.ServiceContext
}

func NewCreateDeploymentLogic(ctx context.Context, svcCtx *svc.ServiceContext) CreateDeploymentLogic {
	return CreateDeploymentLogic{
		Logger: logx.WithContext(ctx),
		ctx:    ctx,
		svcCtx: svcCtx,
	}
}

func (l *CreateDeploymentLogic) CreateDeployment(req types.RequestCreateDeployment) (*types.Response, error) {
	configPath := "k8s/kube.config"

	config, err := clientcmd.BuildConfigFromFlags("", configPath)
	if err != nil {
		log.Fatal(err)
	}
	clientSet, err := kubernetes.NewForConfig(config)
	if err != nil {
		log.Fatal(err)
	}
	//namespace := "default"
	//var replicas int32 = int32(req.Replicas)
	deployment := &appsV1.Deployment{
		ObjectMeta: metav1.ObjectMeta{
			Name:   req.Name,
			Labels: getLabelsMap(req.Labels),
		},
		Spec: appsV1.DeploymentSpec{
			Replicas: &req.Replicas,
			Selector: &metav1.LabelSelector{
				MatchLabels: getSelector(req.Labels, req.Name),
			},
			Template: coreV1.PodTemplateSpec{
				ObjectMeta: metav1.ObjectMeta{
					Name:   req.Name,
					Labels: getSelector(req.Labels, req.Name),
				},
				Spec: coreV1.PodSpec{
					Containers: []coreV1.Container{
						{
							Name:  getImageName(req.Image),
							Image: req.Image,
							Ports: getPorts(req.Ports),
						},
					},
				},
			},
		},
	}
	createDeployment, err := clientSet.AppsV1().Deployments(req.Namespace).Create(context.TODO(), deployment, metav1.CreateOptions{})

	return nil, errorx.NewCodeError(200, "查询成功", createDeployment)
}
func getSelector(labelsStr string, name string) map[string]string {
	selector := getLabelsMap(labelsStr)
	selector["app"] = name
	return selector
}
func getImageName(image string) string {
	// 全部应为数字字母，或前端下拉框选择
	pos := strings.Index(image, ":")
	if pos > 0 {
		return image[:pos]
	}
	return image
}
func getLabelsMap(labelsStr string) map[string]string {
	labelsMap := make(map[string]string)
	labels := strings.Split(labelsStr, "\n")
	for _, label := range labels {
		values := strings.SplitN(label, ":", 2)
		if len(values) != 2 {
			continue
		}
		labelsMap[strings.TrimSpace(values[0])] = strings.TrimSpace(values[1])
	}
	return labelsMap
}
func getPorts(portsStr string) []coreV1.ContainerPort {
	portsList := make([]coreV1.ContainerPort, 0, 5)
	ports := strings.Split(portsStr, "\n")
	for _, port := range ports {
		values := strings.SplitN(port, ":", 3)
		if len(values) != 3 {
			continue
		}
		// todo 端口范围检查
		intPort, err := strconv.Atoi(values[1])
		if err != nil {
			log.Fatal(err)
			continue
		}
		protocol := coreV1.ProtocolTCP
		if strings.Compare(strings.ToLower(values[0]), "tcp") != 0 {
			protocol = coreV1.ProtocolUDP
		}
		portsList = append(portsList, coreV1.ContainerPort{
			Name:          strings.TrimSpace(values[0]),
			ContainerPort: int32(intPort),
			Protocol:      protocol,
		})
	}
	return portsList
}
